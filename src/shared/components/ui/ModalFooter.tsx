interface ModalFooterProps {
  children?: React.ReactNode;
  cancelLabel?: string;
  submitLabel?: string;
  onCancel: () => void;
  onSubmit?: () => void;
  submitting?: boolean;
  submitDisabled?: boolean;
  submitType?: 'submit' | 'button';
}

export default function ModalFooter({
  children,
  cancelLabel = 'Cancel',
  submitLabel = 'Save',
  onCancel,
  onSubmit,
  submitting = false,
  submitDisabled = false,
  submitType = 'submit',
}: ModalFooterProps) {
  return (
    <div className="flex items-center justify-end gap-3 border-t border-border bg-white px-6 py-4">
      {children}
      <button
        type="button"
        onClick={onCancel}
        disabled={submitting}
        className="cursor-pointer rounded-lg border border-border px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50"
      >
        {cancelLabel}
      </button>
      <button
        type={submitType}
        onClick={onSubmit}
        disabled={submitDisabled || submitting}
        className="cursor-pointer rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-dark disabled:opacity-50"
      >
        {submitting ? 'Saving...' : submitLabel}
      </button>
    </div>
  );
}
