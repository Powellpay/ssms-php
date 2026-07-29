import { useState } from 'react';
import { useAppSelector } from '../../../app/store/hooks';
import { useUpdatePassword } from '../../../shared/api/account/accountQueries';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass } from '../../../shared/components/ui/IconField';
import { Lock, Shield, CheckCircle, AlertCircle } from 'lucide-react';

export default function SecurityPage() {
  const user = useAppSelector((s) => s.auth.user);
  const updatePassword = useUpdatePassword();

  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  const emailVerified = !!user?.email_verified_at;

  const handlePasswordChange = async (e: React.FormEvent) => {
    e.preventDefault();
    setSuccessMsg('');

    if (newPassword !== confirmPassword) {
      return;
    }

    await updatePassword.mutateAsync({
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: confirmPassword,
    });

    setSuccessMsg('Password updated successfully.');
    setCurrentPassword('');
    setNewPassword('');
    setConfirmPassword('');
  };

  return (
    <div className="max-w-2xl space-y-6">
      <FormSection title="Email Verification" icon={Shield} description="Your email address verification status">
        <div className="flex items-center gap-3">
          {emailVerified ? (
            <>
              <CheckCircle className="w-5 h-5 text-green-600" />
              <span className="text-sm text-green-700 font-medium">Verified</span>
              <span className="text-sm text-muted">{user?.email}</span>
            </>
          ) : (
            <>
              <AlertCircle className="w-5 h-5 text-amber-500" />
              <span className="text-sm text-amber-700 font-medium">Not Verified</span>
              <span className="text-sm text-muted">{user?.email}</span>
              <button className="ml-auto px-3 py-1.5 text-xs font-medium text-primary border border-primary rounded-lg hover:bg-primary-light cursor-pointer">
                Resend Verification
              </button>
            </>
          )}
        </div>
      </FormSection>

      <form onSubmit={handlePasswordChange}>
        <FormSection title="Change Password" icon={Lock} description="Update your account password">
          <IconField label="Current Password" icon={Lock}>
            <input
              className={inputClass}
              type="password"
              value={currentPassword}
              onChange={(e) => setCurrentPassword(e.target.value)}
              required
            />
          </IconField>

          <IconField label="New Password" icon={Lock}>
            <input
              className={inputClass}
              type="password"
              value={newPassword}
              onChange={(e) => setNewPassword(e.target.value)}
              required
              minLength={8}
            />
          </IconField>

          <IconField label="Confirm New Password" icon={Lock}>
            <input
              className={inputClass}
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              required
              minLength={8}
            />
          </IconField>

          {newPassword && confirmPassword && newPassword !== confirmPassword && (
            <p className="text-xs text-red-600">Passwords do not match</p>
          )}

          {successMsg && (
            <p className="text-sm text-green-600 font-medium">{successMsg}</p>
          )}

          {updatePassword.isError && (
            <p className="text-sm text-red-600">
              {updatePassword.error?.response?.data?.message ?? 'Failed to update password'}
            </p>
          )}

          <div className="flex justify-end pt-2">
            <button
              type="submit"
              disabled={updatePassword.isPending || (newPassword !== confirmPassword)}
              className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
            >
              {updatePassword.isPending ? 'Updating...' : 'Update Password'}
            </button>
          </div>
        </FormSection>
      </form>
    </div>
  );
}
