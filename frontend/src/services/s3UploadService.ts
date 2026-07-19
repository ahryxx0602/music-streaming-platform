import axios from 'axios';

export const s3UploadService = {
  /**
   * Upload file to pre-signed URL using Axios PUT
   * @param file File object from input
   * @param url Pre-signed URL string
   * @param onProgress Callback function for progress (0-100)
   */
  async uploadFile(file: File, url: string, onProgress?: (progress: number) => void): Promise<void> {
    try {
      await axios.put(url, file, {
        headers: {
          'Content-Type': file.type,
        },
        onUploadProgress: (progressEvent) => {
          if (progressEvent.total) {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            if (onProgress) {
              onProgress(percentCompleted);
            }
          }
        },
      });
    } catch (error) {
      console.error('S3 Upload Error:', error);
      throw error;
    }
  }
};
