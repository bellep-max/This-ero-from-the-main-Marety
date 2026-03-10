import { useModal } from 'vue-final-modal';

export const showModal = async (modal, initAttrs = {}) => {
    const { open } = useModal({
        component: modal,
    });

    return await open();
};

export const hideModal = async (modal, initAttrs = {}) => {
    const { close } = useModal({
        component: modal,
    });

    return await close();
};

export const isNotEmpty = (model) => {
    return model?.length > 0;
};

export default { showModal, hideModal, isNotEmpty };
