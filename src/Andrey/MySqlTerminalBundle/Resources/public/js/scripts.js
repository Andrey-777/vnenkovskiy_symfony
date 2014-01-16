/**
 * Created by avnenkovskyi on 1/15/14.
 */
osfipghdfgpfsdgsdfgsdfg
function disabledPass() {
    var checkboxElement = document.getElementById( 'changePass' );
    var inpPass = document.getElementById( 'password' );

    if( checkboxElement.checked ) {
        inpPass.disabled = false;
    }
    else {
        inpPass.disabled = true;
    }
}

function selectQuery() {
    var sel = document.getElementById( 'selectListId' );
    var selText = sel.options[sel.selectedIndex].text;
    var textAr = document.getElementById( 'sqlAreaId' );

    textAr.value = selText;
}