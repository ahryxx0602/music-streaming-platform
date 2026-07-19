<?php

namespace Modules\Music\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Music\Models\Song;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Audio\Aac;

class ProcessAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $song;

    /**
     * Thời gian timeout dài (ví dụ: 1 giờ) để xử lý file audio nặng
     */
    public $timeout = 3600;
    
    /**
     * Số lần retry nếu job thất bại
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->song->update(['processing_status' => 'processing']);

        try {
            // 1. Mở trực tiếp file raw trên S3
            $media = FFMpeg::fromDisk('s3')->open($this->song->original_file_path);
            
            // 2. Trích xuất thời lượng (duration)
            $duration = $media->getDurationInSeconds();
            
            // 3. Khởi tạo định dạng nén AAC 128kbps (chuẩn mượt mà cho HLS)
            $format = new Aac();
            $format->setAudioKiloBitrate(128);

            // 4. Đường dẫn xuất file m3u8
            $hlsPath = 'songs/hls/' . date('Y/m') . '/' . $this->song->id . '/master.m3u8';

            // 5. Convert sang HLS và đẩy thẳng ngược lên S3
            $media->exportForHLS()
                  ->addFormat($format)
                  ->toDisk('s3')
                  ->save($hlsPath);

            // 6. Cập nhật Database thành công
            $this->song->update([
                'duration' => $duration,
                'hls_path' => $hlsPath,
                'processing_status' => 'completed',
            ]);

        } catch (\Exception $e) {
            $this->song->update([
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
                'processing_attempts' => $this->song->processing_attempts + 1,
            ]);
            
            throw $e; // Đẩy lỗi lại cho Queue xử lý retry
        }
    }
}
