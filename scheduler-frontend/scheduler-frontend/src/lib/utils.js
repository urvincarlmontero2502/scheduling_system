// Utility: conditionally join class names (shim version of clsx + twMerge)
export function cn(...inputs) {
  return inputs
    .flat()
    .filter((x) => typeof x === "string" && x.length > 0)
    .join(" ");
}
