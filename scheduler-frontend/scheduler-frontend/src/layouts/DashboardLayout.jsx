import { NavLink, Outlet, useNavigate } from "react-router-dom";
import {
  CalendarClock,
  LayoutGrid,
  ClipboardList,
  Car,
  Building2,
  LogOut,
} from "lucide-react";
import { useAuth } from "../context/AuthContext";

const NAV_ITEMS = [
  { to: "/", label: "Overview", icon: LayoutGrid, end: true },
  { to: "/calendar", label: "Calendar", icon: CalendarClock },
  { to: "/bookings", label: "Bookings", icon: ClipboardList },
  {
    to: "/resources",
    label: "Facilities & Vehicles",
    icon: Building2,
    roles: ["admin"],
  },
];

export default function DashboardLayout() {
  const { user, signOut } = useAuth();
  const navigate = useNavigate();

  async function handleSignOut() {
    await signOut();
    navigate("/login");
  }

  const visibleItems = NAV_ITEMS.filter(
    (item) => !item.roles || item.roles.includes(user?.role),
  );

  return (
    <div className="flex min-h-screen bg-paper">
      {/* Sticky Sidebar */}
      <aside className="sticky top-0 flex h-screen w-60 flex-col border-r border-line bg-white">
        <div className="flex items-center gap-2.5 px-5 py-5">
          <div className="flex h-8 w-8 items-center justify-center rounded-md bg-brand text-white">
            <CalendarClock size={16} />
          </div>
          <p className="text-[14px] font-semibold leading-tight">Scheduler</p>
        </div>

        <nav className="flex-1 px-3 py-2 overflow-y-auto">
          {visibleItems.map(({ to, label, icon: Icon, end }) => (
            <NavLink
              key={to}
              to={to}
              end={end}
              className={({ isActive }) =>
                `mb-1 flex items-center gap-2.5 rounded-md px-3 py-2 text-[13.5px] transition ${
                  isActive
                    ? "bg-brand-light text-brand-dark font-medium"
                    : "text-steel hover:bg-paper hover:text-ink"
                }`
              }>
              <Icon size={16} />
              {label}
            </NavLink>
          ))}
        </nav>

        <div className="border-t border-line px-3 py-3">
          {/* User Profile Section */}
          <div className="mb-2 px-3">
            <p className="truncate text-[13px] font-medium text-ink">
              {user?.username || user?.name || "Signed in user"}
            </p>
            <p className="truncate text-[12px] text-steel">
              {user?.barangay
                ? `Brgy. ${user.barangay}`
                : user?.role || "Staff"}
            </p>
          </div>

          <button
            onClick={handleSignOut}
            className="flex w-full items-center gap-2.5 rounded-md px-3 py-2 text-[13.5px] text-steel transition hover:bg-paper hover:text-status-rejected">
            <LogOut size={16} />
            Sign out
          </button>
        </div>
      </aside>

      {/* Scrollable Main Content */}
      <div className="flex-1 overflow-y-auto">
        <Outlet />
      </div>
    </div>
  );
}
