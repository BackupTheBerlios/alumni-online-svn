// Control flags for list selection and sort sequence
// Sequence is on option value (first 2 chars - can be stripped off in form processing)
// It is assumed that the select list is in sort sequence initially

var singleSelect = true;  // Allows an item to be selected once only
var sortSelect = true;  // Only effective if above flag set to true
var sortPick = true;  // Will order the picklist in sort sequence

// Adds a selected item into the picklist
function addIt(source, pick, target) {
  var selectList = document.getElementById(source);
  var targetList = document.getElementById(target);
  var selectIndex = selectList.selectedIndex;
  var selectOptions = selectList.options;
  var pickList = document.getElementById(pick);
  var pickOptions = pickList.options;
  var pickOLength = pickOptions.length;
  
  // An item must be selected
  if (selectIndex > -1) {
    pickOptions[pickOLength] = new Option(selectList[selectIndex].text);
    pickOptions[pickOLength].value = selectList[selectIndex].value;
	
    // If single selection, remove the item from the select list
    if (singleSelect) {
      selectOptions[selectIndex] = null;
    }

    if (sortPick) {
      var tempText;
      var tempValue;
	  
      // Sort the pick list
      while (pickOLength > 0 && pickOptions[pickOLength].value < pickOptions[pickOLength-1].value) {
        tempText = pickOptions[pickOLength-1].text;
        tempValue = pickOptions[pickOLength-1].value;
        pickOptions[pickOLength-1].text = pickOptions[pickOLength].text;
        pickOptions[pickOLength-1].value = pickOptions[pickOLength].value;
        pickOptions[pickOLength].text = tempText;
        pickOptions[pickOLength].value = tempValue;
        pickOLength = pickOLength - 1;
      }
    }
  }
  
  var tmpArray= new Array(pickList.options.length);
  for (i=0; i<pickList.options.length; i++) {
	  tmpArray[i] = pickList.options[i].value;
  }
  
  targetList.value = tmpArray;
}

// Deletes an item from the picklist
function delIt(source, pick, target) {
  var selectList = document.getElementById(source);
  var targetList = document.getElementById(target);
  var selectOptions = selectList.options;
  var selectOLength = selectOptions.length;
  var pickList = document.getElementById(pick);
  var pickIndex = pickList.selectedIndex;
  var pickOptions = pickList.options;
  
  if (pickIndex > -1) {
    // If single selection, replace the item in the select list
    if (singleSelect) {
      selectOptions[selectOLength] = new Option(pickList[pickIndex].text);
      selectOptions[selectOLength].value = pickList[pickIndex].value;
    }

    pickOptions[pickIndex] = null;
    if (singleSelect && sortSelect) {
      var tempText;
      var tempValue;
      // Re-sort the select list
      while (selectOLength > 0 && selectOptions[selectOLength].value < selectOptions[selectOLength-1].value) {
        tempText = selectOptions[selectOLength-1].text;
        tempValue = selectOptions[selectOLength-1].value;
        selectOptions[selectOLength-1].text = selectOptions[selectOLength].text;
        selectOptions[selectOLength-1].value = selectOptions[selectOLength].value;
        selectOptions[selectOLength].text = tempText;
        selectOptions[selectOLength].value = tempValue;
        selectOLength = selectOLength - 1;
      }
    }
  }
  
  var tmpArray= new Array(pickList.options.length);
  for (i=0; i<pickList.options.length; i++) {
	  tmpArray[i] = pickList.options[i].value;
  }
  
  targetList.value = tmpArray;
}