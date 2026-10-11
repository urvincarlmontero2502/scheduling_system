import { cn } from "../lib/utils";

/**
 * Reusable Skeleton component for loading states.
 *
 * Usage examples:
 *   <Skeleton className="h-4 w-[250px]" />           // text line
 *   <Skeleton className="h-6 w-6 rounded-full" />   // avatar / icon placeholder
 *   <Skeleton className="h-48 w-full rounded-lg" /> // card image
 *
 * The animation uses Tailwind's built-in `animate-pulse` utility.
 */
export function Skeleton({ className, ...props }) {
  return (
    <div
      className={cn(
        "bg-gray-200 animate-pulse rounded",
        className,
      )}
      {...props}
    />
  );
}

// ── Preset shapes ──────────────────────────────────────────────

/** One or more text lines (last line is shorter) */
export const SkeletonText = ({ lines = 1, className = "" }) => (
  <div className={`space-y-1.5 ${className}`}>
    {Array.from({ length: lines }).map((_, i) => (
      <Skeleton
        key={i}
        className={`h-3 bg-gray-200 ${
          i === lines - 1 ? "w-3/4" : "w-full"
        }`}
      />
    ))}
  </div>
);

/** Avatar / profile image placeholder */
export const SkeletonAvatar = ({ size = "h-10 w-10" }) => (
  <Skeleton className={`rounded-full bg-gray-200 ${size}`} />
);

/** Generic stat card (icon + label + value) */
export const SkeletonStatCard = () => (
  <div className="rounded-lg border border-line bg-white p-4 shadow-sm">
    <Skeleton className="h-7 w-7 bg-gray-200 rounded" />
    <Skeleton className="mt-2 h-3 w-4/5 bg-gray-200 rounded" />
    <Skeleton className="mt-1 h-3 w-2/5 bg-gray-200 rounded" />
  </div>
);

/** Resource / facility card (image + text lines) */
export const SkeletonResourceCard = () => (
  <div className="overflow-hidden rounded-lg border border-line bg-white shadow-sm">
    <Skeleton className="h-32 w-full bg-gray-200" />
    <div className="p-4 space-y-2">
      <div className="flex items-start justify-between">
        <Skeleton className="h-4 bg-gray-200 w-3/5 rounded" />
        <Skeleton className="h-5 w-14 bg-gray-200 rounded" />
      </div>
      <Skeleton className="h-3 bg-gray-200 w-full rounded" />
      <Skeleton className="h-3 bg-gray-200 w-4/5 rounded" />
      <Skeleton className="h-3 bg-gray-200 w-2/5 rounded" />
    </div>
  </div>
);

/** Booking row in a table */
export const SkeletonBookingRow = () => (
  <div className="border-b border-line p-4">
    <div className="flex items-start justify-between">
      <div className="space-y-1.5 flex-1">
        <Skeleton className="h-4 bg-gray-200 w-3/5 rounded" />
        <Skeleton className="h-3 bg-gray-200 w-4/5 rounded" />
        <Skeleton className="h-3 bg-gray-200 w-2/5 rounded" />
      </div>
      <Skeleton className="h-6 w-20 bg-gray-200 rounded" />
    </div>
  </div>
);

/** Booking card skeleton for card-view loading */
export const SkeletonBookingCard = () => (
  <div className="overflow-hidden rounded-xl border border-line bg-white shadow-sm">
    <div className="p-4 space-y-3">
      <div className="flex items-start justify-between gap-2">
        <Skeleton className="h-5 w-4/5 bg-gray-200 rounded" />
        <Skeleton className="h-5 w-14 bg-gray-200 rounded-full" />
      </div>
      <Skeleton className="h-3 w-full bg-gray-200 rounded" />
      <Skeleton className="h-3 w-3/4 bg-gray-200 rounded" />
    </div>
    <div className="border-t border-line px-4 py-3 space-y-2">
      <Skeleton className="h-3 w-full bg-gray-200 rounded" />
      <Skeleton className="h-3 w-2/3 bg-gray-200 rounded" />
    </div>
  </div>
);

/** Table-row skeleton for dense table-view loading */
export const SkeletonBookingTableRow = () => (
  <tr className="border-b border-line">
    <td className="px-4 py-3"><Skeleton className="h-3 w-24 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-3 w-20 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-3 w-16 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-3 w-20 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-3 w-24 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-3 w-32 bg-gray-200 rounded" /></td>
    <td className="px-4 py-3"><Skeleton className="h-5 w-16 bg-gray-200 rounded-full" /></td>
    <td className="px-4 py-3"><Skeleton className="h-8 w-20 bg-gray-200 rounded" /></td>
  </tr>
);

export default Skeleton;
