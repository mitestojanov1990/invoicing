import { Routes, Route, Navigate } from 'react-router-dom';
import Layout from './components/Layout';
import InvoicesPage from './pages/InvoicesPage';
import CreateInvoicePage from './pages/CreateInvoicePage';
import EditInvoicePage from './pages/EditInvoicePage';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <Routes>
      {/* Public Routes */}
      <Route
        path='/invoices/create'
        element={
          <Layout>
            <CreateInvoicePage />
          </Layout>
        }
      />

      {/* Protected Routes (Inside Layout) */}
      <Route
        path='/'
        element={
          <ProtectedRoute>
            <Layout children={undefined} />
          </ProtectedRoute>
        }
      >
        <Route index element={<Navigate to='/invoices' />} />
        <Route path='invoices' element={<InvoicesPage />} />
        <Route path='invoices/:id/edit' element={<EditInvoicePage />} />
      </Route>

      {/* 404 Page */}
      <Route path='*' element={<div>404 Not Found</div>} />
    </Routes>
  );
}

export default App;
