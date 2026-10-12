import client from "./client"
import { invalidateCache } from "../utils/cache"

// --- Notifications ------------------------------------------------

export const fetchNotifications = (params) => {
  // Don't use cache for notifications — they should always be fresh
  return client.get("/notifications", { params })
}

export const markNotificationRead = (id) =>
  client.patch(`/notifications/${id}/read`)

export const markAllNotificationsRead = () =>
  client.post("/notifications/mark-all-read")

export const deleteNotification = (id) =>
  client.delete(`/notifications/${id}`)

// Invalidate notifications cache (call after mutations that change notifications)
export const invalidateNotificationsCache = () => {
  try {
    invalidateCache("notifications")
  } catch {}
}
