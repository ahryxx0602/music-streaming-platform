<?php

$tables = [
    'artist_profiles' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->string('stage_name', 100);
            \$table->text('bio')->nullable();
            \$table->string('cover_image')->nullable();
            \$table->boolean('is_verified')->default(false);
            \$table->timestamp('verified_at')->nullable();
            \$table->string('facebook')->nullable();
            \$table->string('instagram')->nullable();
            \$table->string('youtube')->nullable();
            \$table->string('website')->nullable();
            \$table->timestamps();
    ",
    'genres' => "
            \$table->id();
            \$table->foreignId('parent_id')->nullable()->constrained('genres')->nullOnDelete();
            \$table->string('name');
            \$table->string('slug')->unique();
            \$table->string('icon')->nullable();
            \$table->text('description')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
    ",
    'albums' => "
            \$table->id();
            \$table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            \$table->string('title');
            \$table->string('cover_image')->nullable();
            \$table->date('release_date')->nullable();
            \$table->enum('type', ['Single', 'EP', 'Album'])->default('Album');
            \$table->string('status', 50)->default('Draft');
            \$table->text('description')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
    ",
    'songs' => "
            \$table->id();
            \$table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            \$table->foreignId('album_id')->nullable()->constrained('albums')->nullOnDelete();
            \$table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            \$table->string('title');
            \$table->longText('lyrics')->nullable();
            \$table->integer('duration')->default(0);
            \$table->string('original_file_path')->nullable();
            \$table->string('hls_path')->nullable();
            \$table->bigInteger('original_size')->default(0);
            \$table->integer('bitrate')->default(0);
            \$table->integer('sample_rate')->default(0);
            \$table->tinyInteger('channels')->default(2);
            \$table->string('checksum', 64)->nullable()->unique();
            \$table->string('cover_image')->nullable();
            \$table->string('status', 50)->default('Draft');
            \$table->bigInteger('play_count')->default(0);
            \$table->text('rejected_reason')->nullable();
            \$table->timestamp('rejected_at')->nullable();
            \$table->timestamp('approved_at')->nullable();
            \$table->text('processing_error')->nullable();
            \$table->tinyInteger('processing_attempts')->default(0);
            \$table->timestamps();
            \$table->softDeletes();
            \$table->index('title');
            \$table->index('status');
            \$table->index(['status', 'artist_id']);
    ",
    'song_artists' => "
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            \$table->string('role')->default('featured');
            \$table->primary(['song_id', 'artist_id']);
            \$table->timestamps();
    ",
    'playlists' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->string('title');
            \$table->text('description')->nullable();
            \$table->string('cover_image')->nullable();
            \$table->enum('privacy', ['Public', 'Private'])->default('Public');
            \$table->string('type')->default('user')->index();
            \$table->timestamps();
            \$table->softDeletes();
    ",
    'playlist_songs' => "
            \$table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete();
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->integer('position')->default(0);
            \$table->primary(['playlist_id', 'song_id']);
            \$table->timestamps();
            \$table->index(['playlist_id', 'position']);
    ",
    'favorite_songs' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->timestamps();
            \$table->unique(['user_id', 'song_id']);
    ",
    'favorite_albums' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('album_id')->constrained('albums')->cascadeOnDelete();
            \$table->timestamps();
            \$table->unique(['user_id', 'album_id']);
    ",
    'favorite_artists' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            \$table->timestamps();
            \$table->unique(['user_id', 'artist_id']);
    ",
    'favorite_playlists' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete();
            \$table->timestamps();
            \$table->unique(['user_id', 'playlist_id']);
    ",
    'follows' => "
            \$table->id();
            \$table->foreignId('listener_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('artist_id')->constrained('artist_profiles')->cascadeOnDelete();
            \$table->timestamps();
            \$table->unique(['listener_id', 'artist_id']);
    ",
    'artist_invitations' => "
            \$table->id();
            \$table->string('email', 255)->nullable();
            \$table->string('token', 64)->unique();
            \$table->timestamp('expires_at');
            \$table->timestamp('used_at')->nullable();
            \$table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            \$table->timestamps();
    ",
    'listening_histories' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->integer('last_position')->default(0);
            \$table->timestamp('listened_at')->nullable();
            \$table->timestamps();
            \$table->unique(['user_id', 'song_id']);
    ",
    'streams' => "
            \$table->id();
            \$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->string('device')->nullable();
            \$table->binary('ip_address')->nullable();
            \$table->string('session_id')->nullable();
            \$table->integer('duration')->default(0);
            \$table->timestamp('streamed_at')->useCurrent()->index();
    ",
    'recent_searches' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->string('keyword');
            \$table->timestamps();
    ",
    'comments' => "
            \$table->id();
            \$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            \$table->foreignId('song_id')->constrained('songs')->cascadeOnDelete();
            \$table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            \$table->text('content');
            \$table->timestamps();
            \$table->softDeletes();
    ",
    'audit_logs' => "
            \$table->id();
            \$table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            \$table->string('action');
            \$table->string('entity_type')->nullable();
            \$table->bigInteger('entity_id')->nullable();
            \$table->json('old_values')->nullable();
            \$table->json('new_values')->nullable();
            \$table->string('ip_address')->nullable();
            \$table->timestamps();
    ",
    'banners' => "
            \$table->id();
            \$table->string('title');
            \$table->string('image_url');
            \$table->string('target_url')->nullable();
            \$table->integer('order')->default(0);
            \$table->boolean('is_active')->default(true);
            \$table->timestamps();
    ",
    'settings' => "
            \$table->id();
            \$table->string('key')->unique();
            \$table->text('value');
            \$table->string('description')->nullable();
            \$table->timestamps();
    "
];

$modelsData = [
    'Modules/Artist/Models/ArtistProfile.php' => [
        'namespace' => 'Modules\Artist\Models',
        'class' => 'ArtistProfile',
        'fillable' => "['user_id', 'stage_name', 'bio', 'cover_image', 'is_verified', 'verified_at', 'facebook', 'instagram', 'youtube', 'website']",
        'casts' => "['is_verified' => 'boolean', 'verified_at' => 'datetime']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function albums() { return \$this->hasMany(\Modules\Music\Models\Album::class, 'artist_id'); }
    public function songs() { return \$this->hasMany(\Modules\Music\Models\Song::class, 'artist_id'); }
    public function followers() { return \$this->hasMany(\Modules\Artist\Models\Follow::class, 'artist_id'); }
"
    ],
    'Modules/Music/Models/Genre.php' => [
        'namespace' => 'Modules\Music\Models',
        'class' => 'Genre',
        'fillable' => "['parent_id', 'name', 'slug', 'icon', 'description', 'is_active']",
        'casts' => "['is_active' => 'boolean']",
        'methods' => "
    public function parent() { return \$this->belongsTo(self::class, 'parent_id'); }
    public function children() { return \$this->hasMany(self::class, 'parent_id'); }
    public function songs() { return \$this->hasMany(\Modules\Music\Models\Song::class); }
"
    ],
    'Modules/Music/Models/Album.php' => [
        'namespace' => 'Modules\Music\Models',
        'class' => 'Album',
        'fillable' => "['artist_id', 'title', 'cover_image', 'release_date', 'type', 'status', 'description']",
        'casts' => "['release_date' => 'date']",
        'methods' => "
    public function artist() { return \$this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
    public function songs() { return \$this->hasMany(\Modules\Music\Models\Song::class); }
"
    ],
    'Modules/Music/Models/Song.php' => [
        'namespace' => 'Modules\Music\Models',
        'class' => 'Song',
        'fillable' => "['artist_id', 'album_id', 'genre_id', 'title', 'lyrics', 'duration', 'original_file_path', 'hls_path', 'original_size', 'bitrate', 'sample_rate', 'channels', 'checksum', 'cover_image', 'status', 'play_count', 'rejected_reason', 'rejected_at', 'approved_at', 'processing_error', 'processing_attempts']",
        'casts' => "['rejected_at' => 'datetime', 'approved_at' => 'datetime']",
        'methods' => "
    public function artist() { return \$this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
    public function album() { return \$this->belongsTo(\Modules\Music\Models\Album::class); }
    public function genre() { return \$this->belongsTo(\Modules\Music\Models\Genre::class); }
    public function featuredArtists() { return \$this->belongsToMany(\Modules\Artist\Models\ArtistProfile::class, 'song_artists', 'song_id', 'artist_id')->withPivot('role')->withTimestamps(); }
    public function playlists() { return \$this->belongsToMany(\Modules\Playlist\Models\Playlist::class, 'playlist_songs')->withPivot('position')->withTimestamps(); }
    public function comments() { return \$this->hasMany(\Modules\Music\Models\Comment::class); }
    public function streams() { return \$this->hasMany(\Modules\Analytics\Models\Stream::class); }
"
    ],
    'Modules/Music/Models/SongArtist.php' => [
        'namespace' => 'Modules\Music\Models',
        'class' => 'SongArtist',
        'extends' => '\Illuminate\Database\Eloquent\Relations\Pivot',
        'fillable' => "['song_id', 'artist_id', 'role']",
        'methods' => ""
    ],
    'Modules/Playlist/Models/Playlist.php' => [
        'namespace' => 'Modules\Playlist\Models',
        'class' => 'Playlist',
        'fillable' => "['user_id', 'title', 'description', 'cover_image', 'privacy', 'type']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function songs() { return \$this->belongsToMany(\Modules\Music\Models\Song::class, 'playlist_songs')->withPivot('position')->withTimestamps(); }
"
    ],
    'Modules/Playlist/Models/PlaylistSong.php' => [
        'namespace' => 'Modules\Playlist\Models',
        'class' => 'PlaylistSong',
        'extends' => '\Illuminate\Database\Eloquent\Relations\Pivot',
        'fillable' => "['playlist_id', 'song_id', 'position']",
        'methods' => ""
    ],
    'Modules/Artist/Models/ArtistInvitation.php' => [
        'namespace' => 'Modules\Artist\Models',
        'class' => 'ArtistInvitation',
        'fillable' => "['email', 'token', 'expires_at', 'used_at', 'created_by']",
        'casts' => "['expires_at' => 'datetime', 'used_at' => 'datetime']",
        'methods' => "
    public function createdBy() { return \$this->belongsTo(\App\Models\User::class, 'created_by'); }
"
    ],
    'Modules/Artist/Models/Follow.php' => [
        'namespace' => 'Modules\Artist\Models',
        'class' => 'Follow',
        'fillable' => "['listener_id', 'artist_id']",
        'methods' => "
    public function listener() { return \$this->belongsTo(\App\Models\User::class, 'listener_id'); }
    public function artist() { return \$this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
"
    ],
    'Modules/Analytics/Models/ListeningHistory.php' => [
        'namespace' => 'Modules\Analytics\Models',
        'class' => 'ListeningHistory',
        'fillable' => "['user_id', 'song_id', 'last_position', 'listened_at']",
        'casts' => "['listened_at' => 'datetime']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function song() { return \$this->belongsTo(\Modules\Music\Models\Song::class); }
"
    ],
    'Modules/Analytics/Models/Stream.php' => [
        'namespace' => 'Modules\Analytics\Models',
        'class' => 'Stream',
        'fillable' => "['user_id', 'song_id', 'device', 'ip_address', 'session_id', 'duration', 'streamed_at']",
        'casts' => "['streamed_at' => 'datetime']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function song() { return \$this->belongsTo(\Modules\Music\Models\Song::class); }
"
    ],
    'Modules/Users/Models/RecentSearch.php' => [
        'namespace' => 'Modules\Users\Models',
        'class' => 'RecentSearch',
        'fillable' => "['user_id', 'keyword']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
"
    ],
    'Modules/Music/Models/Comment.php' => [
        'namespace' => 'Modules\Music\Models',
        'class' => 'Comment',
        'fillable' => "['user_id', 'song_id', 'parent_id', 'content']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function song() { return \$this->belongsTo(\Modules\Music\Models\Song::class); }
    public function parent() { return \$this->belongsTo(self::class, 'parent_id'); }
    public function replies() { return \$this->hasMany(self::class, 'parent_id'); }
"
    ],
    'Modules/Administration/Models/AuditLog.php' => [
        'namespace' => 'Modules\Administration\Models',
        'class' => 'AuditLog',
        'fillable' => "['user_id', 'action', 'entity_type', 'entity_id', 'old_values', 'new_values', 'ip_address']",
        'casts' => "['old_values' => 'array', 'new_values' => 'array']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
"
    ],
    'Modules/Administration/Models/Banner.php' => [
        'namespace' => 'Modules\Administration\Models',
        'class' => 'Banner',
        'fillable' => "['title', 'image_url', 'target_url', 'order', 'is_active']",
        'casts' => "['is_active' => 'boolean']",
        'methods' => ""
    ],
    'Modules/Administration/Models/Setting.php' => [
        'namespace' => 'Modules\Administration\Models',
        'class' => 'Setting',
        'fillable' => "['key', 'value', 'description']",
        'methods' => ""
    ],
    'Modules/Users/Models/FavoriteSong.php' => [
        'namespace' => 'Modules\Users\Models',
        'class' => 'FavoriteSong',
        'fillable' => "['user_id', 'song_id']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function song() { return \$this->belongsTo(\Modules\Music\Models\Song::class); }
"
    ],
    'Modules/Users/Models/FavoriteAlbum.php' => [
        'namespace' => 'Modules\Users\Models',
        'class' => 'FavoriteAlbum',
        'fillable' => "['user_id', 'album_id']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function album() { return \$this->belongsTo(\Modules\Music\Models\Album::class); }
"
    ],
    'Modules/Users/Models/FavoriteArtist.php' => [
        'namespace' => 'Modules\Users\Models',
        'class' => 'FavoriteArtist',
        'fillable' => "['user_id', 'artist_id']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function artist() { return \$this->belongsTo(\Modules\Artist\Models\ArtistProfile::class, 'artist_id'); }
"
    ],
    'Modules/Users/Models/FavoritePlaylist.php' => [
        'namespace' => 'Modules\Users\Models',
        'class' => 'FavoritePlaylist',
        'fillable' => "['user_id', 'playlist_id']",
        'methods' => "
    public function user() { return \$this->belongsTo(\App\Models\User::class); }
    public function playlist() { return \$this->belongsTo(\Modules\Playlist\Models\Playlist::class); }
"
    ]
];


// Write Migrations
$files = glob(__DIR__ . '/Modules/*/Database/migrations/*_create_*_table.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    foreach ($tables as $tableName => $schema) {
        if (str_contains($file, "create_{$tableName}_table.php")) {
            // Replace up method
            $pattern = '/Schema::create\(\''.$tableName.'\', function \(Blueprint \$table\) \{(.*?)\}\);/s';
            $replacement = "Schema::create('{$tableName}', function (Blueprint \$table) {\n{$schema}\n        });";
            $content = preg_replace($pattern, $replacement, $content);
            
            // Wait, what if table name is like song_artists?
            if (str_contains($schema, 'timestamps()')) {
                // Already replaced
            }
        }
    }
    file_put_contents($file, $content);
}

// Write Models
foreach ($modelsData as $path => $data) {
    $fullPath = __DIR__ . '/' . $path;
    if (!file_exists($fullPath)) continue;
    $extends = $data['extends'] ?? 'Model';
    $traits = $data['traits'] ?? 'HasFactory';
    $casts = isset($data['casts']) ? "protected function casts(): array { return {$data['casts']}; }" : "";
    
    // Add SoftDeletes if needed (e.g., songs, albums, playlists, comments)
    if (in_array($data['class'], ['Song', 'Album', 'Playlist', 'Comment'])) {
        $traits .= ", \Illuminate\Database\Eloquent\SoftDeletes";
    }

    $classContent = "<?php\n\nnamespace {$data['namespace']};\n\n";
    if ($extends === 'Model') {
        $classContent .= "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\n\n";
    } else {
        $classContent .= "use {$data['extends']};\n\n";
        $extends = basename(str_replace('\\', '/', $data['extends']));
    }
    
    $classContent .= "class {$data['class']} extends {$extends}\n{\n";
    if (str_contains($traits, 'HasFactory')) {
        $classContent .= "    use {$traits};\n\n";
    }
    
    $classContent .= "    protected \$fillable = {$data['fillable']};\n";
    if ($casts) {
        $classContent .= "    {$casts}\n";
    }
    
    $classContent .= $data['methods'];
    
    $classContent .= "}\n";
    file_put_contents($fullPath, $classContent);
}

echo "Migrations and Models patched.\n";

// Patch App\Models\User.php
$userPath = __DIR__ . '/app/Models/User.php';
$userContent = file_get_contents($userPath);
$userModel = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected \$fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'status',
        'notification_prefs',
    ];

    protected \$hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_prefs' => 'array',
        ];
    }

    public function artistProfile() { return \$this->hasOne(\Modules\Artist\Models\ArtistProfile::class); }
    public function playlists() { return \$this->hasMany(\Modules\Playlist\Models\Playlist::class); }
    public function listeningHistories() { return \$this->hasMany(\Modules\Analytics\Models\ListeningHistory::class); }
    public function favoriteSongs() { return \$this->hasMany(\Modules\Users\Models\FavoriteSong::class); }
    public function favoriteAlbums() { return \$this->hasMany(\Modules\Users\Models\FavoriteAlbum::class); }
    public function favoriteArtists() { return \$this->hasMany(\Modules\Users\Models\FavoriteArtist::class); }
    public function favoritePlaylists() { return \$this->hasMany(\Modules\Users\Models\FavoritePlaylist::class); }
    public function comments() { return \$this->hasMany(\Modules\Music\Models\Comment::class); }
}
";
file_put_contents($userPath, $userModel);
echo "App\Models\User.php patched.\n";
