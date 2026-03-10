import { useModal } from 'vue-final-modal';

export const showModal = async (modal: any, initAttrs: Record<string, any> = {}) => {
    const { open } = useModal({
        component: modal,
    });

    return await open();
};

export const hideModal = async (modal: any, initAttrs: Record<string, any> = {}) => {
    const { close } = useModal({
        component: modal,
    });

    return await close();
};

export const isNotEmpty = (model: any): boolean => {
    return model?.length > 0;
};

export default { showModal, hideModal, isNotEmpty };
