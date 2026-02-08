export function capitalizeFirstLetter(val: unknown): string {
  return String(val).charAt(0).toUpperCase() + String(val).slice(1);
}
