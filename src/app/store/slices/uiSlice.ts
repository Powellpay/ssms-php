import { createSlice, type PayloadAction } from '@reduxjs/toolkit';

interface UiState {
  sidebarOpen: boolean;
  currentModule: string | null;
}

const initialState: UiState = {
  sidebarOpen: true,
  currentModule: null,
};

const uiSlice = createSlice({
  name: 'ui',
  initialState,
  reducers: {
    toggleSidebar(state) {
      state.sidebarOpen = !state.sidebarOpen;
    },
    setCurrentModule(state, action: PayloadAction<string | null>) {
      state.currentModule = action.payload;
    },
  },
});

export const { toggleSidebar, setCurrentModule } = uiSlice.actions;
export default uiSlice.reducer;
