// Format number as Peruvian Soles
export function formatSoles(value) {
  if (!value) return 'S/. 0.00';
  return new Intl.NumberFormat('es-PE', {
    style: 'currency',
    currency: 'PEN',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
}

// Format simple number with 2 decimals
export function formatNumber(value) {
  if (!value) return '0.00';
  return parseFloat(value).toFixed(2);
}

// Format date
export function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('es-PE');
}

// Format date time
export function formatDateTime(date) {
  if (!date) return '';
  return new Date(date).toLocaleString('es-PE');
}
