const useDismissModal = () => {
  const dismiss = () => {
    const closeButton = document.querySelector('[data-slot="dialog-close"]') as HTMLButtonElement;
    closeButton?.click();
  };
  return {
    dismiss,
  };
};

export default useDismissModal;
