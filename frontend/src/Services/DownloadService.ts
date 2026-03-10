import apiClient from '@/api/client';
import { DOWNLOADS } from '@/api/endpoints';

export const download = async (uuid: string) => {
    const response = await apiClient.get(DOWNLOADS.DOWNLOAD(uuid), {
        responseType: 'blob',
    });
    return response;
};

export const downloadHd = async (uuid: string) => {
    const response = await apiClient.get(DOWNLOADS.DOWNLOAD_HD(uuid), {
        responseType: 'blob',
    });
    return response;
};

export default { download, downloadHd };
