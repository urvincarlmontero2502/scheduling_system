import { Bell } from "lucide-react"
import { useNotifications } from "../context/NotificationContext"

export default function NotificationBell({ onClick }) {
  const { unreadCount, loading } = useNotifications()

  return (
    <button
      type="button"
      aria-label="Notifications"
      onClick={onClick}
      className="relative flex h-10 w-10 items-center justify-center rounded-lg text-steel hover:bg-paper hover:text-ink transition-colors"
    >
      <Bell size={20} />
      {unreadCount > 0 && (
        <span className="absolute top-1.5 right-1.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold leading-none text-white px-0.5">
          {unreadCount > 99 ? "99+" : unreadCount}
        </span>
      )}
    </button>
  )
}
