import { BrowserRouter, Routes, Route } from 'react-router-dom'
import { AuthProvider } from './context/AuthContext'
import ProtectedRoute from './components/ProtectedRoute'
import DashboardLayout from './layouts/DashboardLayout'
import Login from './pages/Login'
import Overview from './pages/Overview'
import Calendar from './pages/Calendar'
import Bookings from './pages/Bookings'
import Resources from './pages/Resources'

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route
            path="/"
            element={
              <ProtectedRoute>
                <DashboardLayout />
              </ProtectedRoute>
            }
          >
            <Route index element={<Overview />} />
            <Route path="calendar" element={<Calendar />} />
            <Route path="bookings" element={<Bookings />} />
            <Route path="resources" element={<Resources />} />
          </Route>
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}
