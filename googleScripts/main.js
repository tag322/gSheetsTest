function doPost(e) {
    try {
  
      var data = JSON.parse(e.postData.contents).values; 
  
      if (!Array.isArray(data)) {
        throw new Error("Данные должны быть массивом.");
      } 
      if(data.length < 3) {
        throw new Error("Данные должны быть массивом минимум из трех элементов.");
      }
  
      var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
      var allowedValues = ["Allowed", "Prohibited"];
      
      var existingValues = sheet.getRange("A:A").getValues().flat().filter(String);
  
      var firstColumnValue = Number(data[0]);
      var secondColumnValue = data[1];
      var thirdColumnValue = data[2];
  
      if (isNaN(firstColumnValue) || existingValues.includes(Number(firstColumnValue))) {
        throw new Error(`Неверное или неуникальное значение в первой колонке: ${firstColumnValue}`);
      }
  
      if (!allowedValues.includes(secondColumnValue)) {
        throw new Error(`Неверное значение во второй колонке: ${secondColumnValue}`);
      }
  
      if (thirdColumnValue == '') {
        throw new Error(`Значение в третьей колонке не должно быть пустой строкой`);
      }
  
      sheet.appendRow(data);
  
      return ContentService.createTextOutput(JSON.stringify({ status: "success" }))
        .setMimeType(ContentService.MimeType.JSON);
  
    } catch (error) {
      Logger.log("Ошибка: " + error.message);
      return ContentService.createTextOutput(JSON.stringify({ 
          status: "error",
          message: error.message
        }))
        .setMimeType(ContentService.MimeType.JSON);
    }
  }
  
  function doGet(e) {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Лист1");
    // var range = sheet.getDataRange();
    var columnNames = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues();
    var numRows = parseInt(e.parameter.limit, 10);
    var pageOffset = (e.parameter.page - 1) * numRows + 2
    var range = sheet.getRange(pageOffset, 1, Math.min(numRows, sheet.getLastRow()), sheet.getLastColumn());
    var values = range.getValues();
  
    var finalData = [columnNames[0]].concat(values);
    
    var normalizedValues = finalData.map(row => 
      row.map(cell => cell === undefined || cell === null ? "" : cell)
    );
  
    var totalRows = sheet.getLastRow();
    var totalColumns = sheet.getLastColumn();
  
    var response = {
      tablesize: {
        totalRows: totalRows,  
        totalColumns: totalColumns, 
      },
      table: normalizedValues
    };
    
    return ContentService.createTextOutput(JSON.stringify(response))
      .setMimeType(ContentService.MimeType.JSON);
  }

  function onEdit(e) {
    const sheet = e.source.getActiveSheet();
    const range = sheet.getDataRange();
    const values = range.getValues();
  
    const nonEmptyRows = values.filter(row => {
      return !row.every(cell => cell === ''); 
    });
  
    sheet.clearContents();
  
    if (nonEmptyRows.length > 0) {
      sheet.getRange(1, 1, nonEmptyRows.length, nonEmptyRows[0].length).setValues(nonEmptyRows);
    }
  }