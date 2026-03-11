export const showModal = async (_modal: any, _initAttrs: Record<string, any> = {}) => {
    console.warn('MiscService.showModal is a no-op in React. Use component state to control modals.');
};

export const hideModal = async (_modal: any, _initAttrs: Record<string, any> = {}) => {
    console.warn('MiscService.hideModal is a no-op in React. Use component state to control modals.');
};

export const isNotEmpty = (model: any): boolean => {
    return model?.length > 0;
};

export default { showModal, hideModal, isNotEmpty };
