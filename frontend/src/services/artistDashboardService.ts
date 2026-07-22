import api from './api';

export interface DashboardStats {
  total_followers: number;
  total_streams: number;
  total_tracks: number;
  balance_usd: number;
}

export interface TopTrack {
  id: number;
  title: string;
  streams: number;
  cover: string;
  trend: string;
}

export const artistDashboardService = {
  getStats: async (): Promise<DashboardStats> => {
    const { data } = await api.get('/v1/artist/dashboard/stats');
    return data.data;
  },

  getTopTracks: async (): Promise<TopTrack[]> => {
    const { data } = await api.get('/v1/artist/dashboard/top-tracks');
    return data.data;
  },

  getStreamsChart: async (): Promise<number[]> => {
    const { data } = await api.get('/v1/artist/dashboard/streams-chart');
    return data.data;
  }
};
