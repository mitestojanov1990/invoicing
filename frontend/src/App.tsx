import { Routes, Route, Navigate } from 'react-router-dom';
import MainPage from './pages/MainPage';
import ProtectedRoute from './components/ProtectedRoute';
import SignInPage from './pages/SignInPage';
import SignUpPage from './pages/SignUpPage';
import InvoicesPage from './pages/InvoicesPage';

function App() {
  return (
    <Routes>
      <Route path='/' element={<Navigate to='/invoices' />} />
      <Route
        path='/invoices'
        element={
          <ProtectedRoute>
            <InvoicesPage />
          </ProtectedRoute>
        }
      />
      <Route path='/signin' element={<SignInPage />} />
      <Route path='/signup' element={<SignUpPage />} />
      <Route path='*' element={<div>404 Not Found</div>} />
    </Routes>
  );
}

export default App;
