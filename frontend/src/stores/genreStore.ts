import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/services/api'; // Assuming you have an axios instance setup here

export const useGenreStore = defineStore('genre', () => {
  const genresTree = ref([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  // Flatten tree into a 1D array for dropdowns
  const flattenGenres = (genres: any[], level = 0, prefix = '') => {
    let result: any[] = [];
    genres.forEach((genre) => {
      result.push({
        ...genre,
        level,
        displayName: prefix + genre.name
      });
      if (genre.children && genre.children.length > 0) {
        result = result.concat(flattenGenres(genre.children, level + 1, prefix + '-- '));
      }
    });
    return result;
  };

  const fetchGenres = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await api.get('/admin/genres');
      if (response.data && response.data.success) {
        genresTree.value = response.data.data;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi tải danh sách thể loại';
    } finally {
      isLoading.value = false;
    }
  };

  const saveGenre = async (id: number | null, payload: any) => {
    isLoading.value = true;
    error.value = null;
    try {
      if (id) {
        await api.put(`/admin/genres/${id}`, payload);
      } else {
        await api.post('/admin/genres', payload);
      }
      await fetchGenres(); // refresh the tree
      return true;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu thể loại';
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    genresTree,
    isLoading,
    error,
    fetchGenres,
    saveGenre,
    flattenGenres
  };
});
