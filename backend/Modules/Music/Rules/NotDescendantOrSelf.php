<?php

namespace Modules\Music\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Music\Models\Genre;

class NotDescendantOrSelf implements ValidationRule
{
    protected $genreId;

    public function __construct($genreId)
    {
        $this->genreId = $genreId;
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (empty($value)) return;

        // Tối ưu hóa 100%: Sử dụng Recursive CTE (MySQL 8.0+ / PostgreSQL)
        // Lội ngược dòng từ $value (parent_id mới) lên gốc chỉ bằng 1 câu Query duy nhất.
        $query = "
            WITH RECURSIVE genre_path AS (
                SELECT id, parent_id
                FROM genres
                WHERE id = ?
                
                UNION ALL
                
                SELECT g.id, g.parent_id
                FROM genres g
                INNER JOIN genre_path gp ON gp.parent_id = g.id
            )
            SELECT id FROM genre_path WHERE id = ? LIMIT 1
        ";

        $result = \Illuminate\Support\Facades\DB::select($query, [$value, $this->genreId]);

        if (!empty($result)) {
            $fail('admin.genres.infinite_loop_error');
        }
    }
}
