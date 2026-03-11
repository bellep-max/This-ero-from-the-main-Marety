import apiClient from '@/api/client';
import { DOWNLOADS } from '@/api/endpoints';

export const download = async (uuid: string, type: string = 'song') => {
    const response = await apiClient.get(DOWNLOADS.DOWNLOAD(type, uuid), {
        responseType: 'blob',
    });
    return response;
};

export const downloadHd = async (uuid: string, type: string = 'song') => {
    const response = await apiClient.get(DOWNLOADS.DOWNLOAD_HD(type, uuid), {
        responseType: 'blob',
    });
    return response;
};

export default { download, downloadHd };
