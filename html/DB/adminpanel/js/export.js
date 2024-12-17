// CSV Olarak İndirme
function exportTableToCSV(tableSelector, fileName, columnCount) {
    const rows = document.querySelectorAll(`${tableSelector} tr`);
    const csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach((col, colIndex) => {
            if (colIndex < columnCount) {
                rowData.push(`"${col.innerText.trim()}"`);
            }
        });
        csv.push(rowData.join(","));
    });

    const csvContent = csv.join("\n");
    downloadFile(csvContent, fileName, "csv");
}


// XML Olarak İndirme
function exportTableToXML(tableSelector, fileName, columnCount) {
    const rows = document.querySelectorAll(`${tableSelector} tr`);
    let xml = `<?xml version="1.0" encoding="UTF-8"?><sales>`;

    rows.forEach((row, index) => {
        const cols = row.querySelectorAll('th, td');
        if (index > 0) {
            xml += `<sale>`;
            cols.forEach((col, colIndex) => {
                if (colIndex < columnCount) {
                    const header = rows[0].querySelectorAll('th')[colIndex].innerText.trim();
                    xml += `<${header.replace(/\s+/g, "")}>${col.innerText.trim()}</${header.replace(/\s+/g, "")}>`;
                }
            });
            xml += `</sale>`;
        }
    });

    xml += `</sales>`;
    downloadFile(xml, fileName, "xml");
}


// TXT Olarak İndirme
function exportTableToTXT(tableSelector, fileName, columnCount) {
    const rows = document.querySelectorAll(`${tableSelector} tr`);
    let txtContent = "";

    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach((col, colIndex) => {
            if (colIndex < columnCount) rowData.push(col.innerText.trim());
        });
        txtContent += rowData.join("\t") + "\n";
    });

    downloadFile(txtContent, fileName, "txt");
}


// JSON Olarak İndirme
function exportTableToJSON(tableSelector, fileName, columnCount) {
    const rows = document.querySelectorAll(`${tableSelector} tr`);
    const jsonData = [];
    const headers = [];

    rows[0].querySelectorAll('th').forEach((header, index) => {
        if (index < columnCount) headers.push(header.innerText.trim());
    });

    rows.forEach((row, index) => {
        if (index === 0) return;
        const cols = row.querySelectorAll('td');
        const rowData = {};
        cols.forEach((col, colIndex) => {
            if (colIndex < headers.length) rowData[headers[colIndex]] = col.innerText.trim();
        });
        jsonData.push(rowData);
    });

    const jsonString = JSON.stringify(jsonData, null, 4);
    downloadFile(jsonString, fileName, "json");
}


// PDF Olarak İndirme
function exportTableToPDF(tableSelector, fileName, columnCount) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'pt', 'a4');
    const rows = document.querySelectorAll(`${tableSelector} tr`);
    const tableData = [];
    let tableHeader = [];

    rows.forEach((row, index) => {
        const cols = row.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach((col, colIndex) => {
            if (colIndex < columnCount) rowData.push(col.innerText.trim());
        });
        if (index === 0) tableHeader = rowData;
        else tableData.push(rowData);
    });

    pdf.autoTable({
        head: [tableHeader],
        body: tableData,
        theme: 'grid',
        styles: { fontSize: 10, cellPadding: 5 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255 }
    });

    pdf.save(fileName + ".pdf");
}


// Dosya İndirme Fonksiyonu
function downloadFile(content, fileName, extension) {
    const blob = new Blob([content], { type: `application/${extension}` });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `${fileName}.${extension}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
