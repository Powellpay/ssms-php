import { useState, useRef } from 'react';
import { useAppSelector, useAppDispatch } from '../../../app/store/hooks';
import { setCredentials } from '../../../app/store/slices/authSlice';
import { useUpdateProfile } from '../../../shared/api/account/accountQueries';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass } from '../../../shared/components/ui/IconField';
import {
  User, Mail, Phone, Camera, Save,
} from 'lucide-react';

export default function ProfileSettingsPage() {
  const user = useAppSelector((s) => s.auth.user);
  const dispatch = useAppDispatch();
  const updateProfile = useUpdateProfile();

  const [editing, setEditing] = useState(false);
  const [name, setName] = useState(user?.name ?? '');
  const [email, setEmail] = useState(user?.email ?? '');
  const [phone, setPhone] = useState(user?.phone ?? '');
  const [avatarFile, setAvatarFile] = useState<File | null>(null);
  const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);

  const avatarSrc = avatarPreview ?? user?.avatar ?? null;

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setAvatarFile(file);
    setAvatarPreview(URL.createObjectURL(file));
  };

  const handleSave = async () => {
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    if (phone) formData.append('phone', phone);
    if (avatarFile) formData.append('avatar', avatarFile);

    const updated = await updateProfile.mutateAsync(formData);
    if (updated) {
      dispatch(setCredentials({ user: updated, token: localStorage.getItem('token') ?? '' }));
    }
    setEditing(false);
    setAvatarFile(null);
  };

  const handleCancel = () => {
    setName(user?.name ?? '');
    setEmail(user?.email ?? '');
    setPhone(user?.phone ?? '');
    setAvatarFile(null);
    setAvatarPreview(null);
    setEditing(false);
  };

  return (
    <div className="max-w-2xl space-y-6">
      <FormSection title="Personal Information" icon={User}>
        <div className="flex items-center gap-4 mb-4">
          <div className="relative">
            <div className="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-bold overflow-hidden">
              {avatarSrc ? (
                <img src={avatarSrc} alt="avatar" className="w-full h-full object-cover" />
              ) : (
                user?.name?.charAt(0) ?? 'U'
              )}
            </div>
            {editing && (
              <button
                type="button"
                onClick={() => fileRef.current?.click()}
                className="absolute -bottom-1 -right-1 w-7 h-7 bg-white border border-border rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50 cursor-pointer"
              >
                <Camera className="w-3.5 h-3.5 text-gray-500" />
              </button>
            )}
            <input
              ref={fileRef}
              type="file"
              accept="image/*"
              className="hidden"
              onChange={handleFileChange}
            />
          </div>
          <div>
            <p className="font-medium text-gray-900">{user?.name}</p>
            <p className="text-sm text-muted">{user?.role?.role_name}</p>
          </div>
        </div>

        <IconField label="Full Name" icon={User}>
          <input
            className={inputClass}
            value={name}
            onChange={(e) => setName(e.target.value)}
            disabled={!editing}
          />
        </IconField>

        <IconField label="Email" icon={Mail}>
          <input
            className={inputClass}
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            disabled={!editing}
          />
        </IconField>

        <IconField label="Phone" icon={Phone}>
          <input
            className={inputClass}
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
            disabled={!editing}
          />
        </IconField>
      </FormSection>

      <div className="flex justify-end gap-3">
        {editing ? (
          <>
            <button
              onClick={handleCancel}
              className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer"
            >
              Cancel
            </button>
            <button
              onClick={handleSave}
              disabled={updateProfile.isPending}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
            >
              <Save className="w-4 h-4" />
              {updateProfile.isPending ? 'Saving...' : 'Save Changes'}
            </button>
          </>
        ) : (
          <button
            onClick={() => setEditing(true)}
            className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"
          >
            Edit Profile
          </button>
        )}
      </div>

      {updateProfile.isError && (
        <p className="text-sm text-red-600">{updateProfile.error?.response?.data?.message ?? 'Failed to update profile'}</p>
      )}
    </div>
  );
}
