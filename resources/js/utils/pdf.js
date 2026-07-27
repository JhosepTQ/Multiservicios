// Download PDF from URL
export async function downloadPdf(url, fileName) {
  try {
    const response = await axios.get(url, {
      responseType: 'blob'
    });

    const blob = new Blob([response.data], { type: 'application/pdf' });
    const link = document.createElement('a');
    const urlBlob = window.URL.createObjectURL(blob);
    
    link.href = urlBlob;
    link.download = fileName || 'documento.pdf';
    document.body.appendChild(link);
    link.click();
    
    window.URL.revokeObjectURL(urlBlob);
    document.body.removeChild(link);
  } catch (error) {
    console.error('Error downloading PDF:', error);
    throw error;
  }
}

// Open PDF in new tab
export async function viewPdf(url) {
  try {
    const response = await axios.get(url, {
      responseType: 'blob'
    });

    const blob = new Blob([response.data], { type: 'application/pdf' });
    const urlBlob = window.URL.createObjectURL(blob);
    window.open(urlBlob);
  } catch (error) {
    console.error('Error viewing PDF:', error);
    throw error;
  }
}
