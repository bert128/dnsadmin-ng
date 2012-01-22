/* $Id: util.js,v 1.1 2007/12/14 15:01:40 bert Exp $ */

function showElement(name) {
        var obj = document.getElementById(name);
        obj.style.display = 'block';
}
function hideElement(name) {
        var obj = document.getElementById(name);
        obj.style.display = 'none';
}
function toggleElement(name) {
        var obj = document.getElementById(name);
        if (obj.style.display == '' || obj.style.display == 'none') {
                obj.style.display = 'block';
        } else {
                obj.style.display = 'none';
        }
}

function setHeight(name, value) {
	var obj = document.getElementById(name);

	if (obj.type == 'select' || obj.type == 'select-multiple') {
	    obj.size = value;
	} else {
	    obj.rows = value;
	}
}

function adjustHeight(name, amount) {
	var obj = document.getElementById(name);

	if (obj.type == 'select' || obj.type == 'select-multiple') {
	    if (obj.size + amount > 0) {
		obj.size += amount;
	    }
	} else {
	    if (obj.rows + amount > 0) {
	    	obj.rows += amount;
	    }
	}
}

function confirmAction(msg) {
	var agree = confirm(msg);
	if (agree)
		return(true);
	else
		return(false);
}

function moveAttributes(from,to) {
   var optionList = from.options;
    var continuevar=0;
        while(1) {	
	continuevar=0;
    if (optionList.length==0) { 
       selectNone(from,to);
       setSize(from,to);
       return;
    }	
    for (var intLoop=0; intLoop < optionList.length; intLoop++) {
	if (from.options.item(intLoop).selected) {
	   to.appendChild(from.options.item(intLoop));		           		
	   continuevar=1;
	}
   }
   if (continuevar) continue;	
   selectNone(from,to);
   setSize(from,to);
   return;
  }
}

function setSize(list1,list2){
    list1.size = getSize(list1);
    list2.size = getSize(list2);
}

function selectNone(list1,list2){
    list1.selectedIndex = -1;
    list2.selectedIndex = -1;
    addIndex = -1;
    selIndex = -1;
}

function getSize(list){
    /* Mozilla ignores whitespace, 
       IE doesn't - count the elements 
       in the list */
    var len = list.childNodes.length;
    var nsLen = 0;
    //nodeType returns 1 for elements
    for(i=0; i<len; i++){
        if(list.childNodes.item(i).nodeType==1)
            nsLen++;
    }
    if(nsLen<2)
        return 2;
    else
        return nsLen;
}

function moveAll(from,to) {
   while(from.options.item(0)) {	 
        to.appendChild(from.options.item(0));		  
   }   
   selectNone(from,to);
   setSize(from,to);
}

function getSelected(opt) {
      var selected = new Array();
      var index = 0;
      for (var intLoop=0; intLoop < opt.length; intLoop++) {
         if (opt[intLoop].selected) {
            index = selected.length;
            selected[index] = new Object;
            selected[index].value = opt[intLoop].value;
            selected[index].index = intLoop;
         }
      }
      return selected;
}

function formSubmit(opt,formName){
   for (var loop=0; loop<opt.length;loop++) {
	opt[loop].selected=true;
   }
   document.forms[formName].submit();
}function insertTags(tagOpen, tagClose, sampleText) {
        //var txtarea = document.forms.editor;
        var txtarea = document.getElementById('editor');

        if(txtarea.selectionStart || txtarea.selectionStart == '0') {
                var replaced = false;
                var startPos = txtarea.selectionStart;
                var endPos       = txtarea.selectionEnd;
                if(endPos - startPos) replaced = true;
                var scrollTop=txtarea.scrollTop;
                var myText = (txtarea.value).substring(startPos, endPos);
                if(!myText) { myText=sampleText;}
                if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
                        subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
                } else {
                        subst = tagOpen + myText + tagClose;
                }
                txtarea.value = txtarea.value.substring(0, startPos) + subst + txtarea.value.substring(endPos, txtarea.value.length);
                                                                                
                txtarea.focus();

                //set new selection
                if(replaced){
                        var cPos=startPos+(tagOpen.length+myText.length+tagClose.length);
                        txtarea.selectionStart=cPos;
                        txtapea.selectionEnd=cPos;
                }else{
                        txtarea.selectionStart=startPos+tagOpen.length;
                        txtarea.selectionEnd=startPos+tagOpen.length+myText.length;
                }
                txtarea.scrollTop=scrollTop;
        // others
        } else {
                var copy_alertText=alertText;
                var re1=new RegExp("\\$1","g");
                var re2=new RegExp("\\$2","g");
                copy_alertText=copy_alertText.replace(re1,sampleText);
                copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
                var text;
                if (sampleText) {
                        text=prompt(copy_alertText);
                } else {
                        text="";
                }
                if(!text) { text=sampleText;}
                text=tagOpen+text+tagClose;
                //append to the end
                txtarea.value += "\n"+text;

                // in Safari this causes scrolling
                if(!is_safari) {
                        txtarea.focus();
                }

        }
        // reposition cursor if possible
        if (txtarea.createTextRange) txtarea.caretPos = 
document.selection.createRange().duplicate();
}

