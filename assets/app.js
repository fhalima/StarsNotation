/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';


//--------------------------------Gestion de la notation en étoiles -------------------------------//
let firstconnect = 1;
if (firstconnect === 1) {
    SetStartsGold($("#note_form_value").val());
    SetStartsGray(parseInt($("#note_form_value").val()) + 1);
    firstconnect = 0;
}
for (let i = 1; i <= 5; i++) {
    $("#star" + i).click(function () {
        $("#note_form_value").val(i);
        SetStartsGold(i);
        SetStartsGray(i + 1);
    });

    // let note_value = $("#note_form_value").val();

    $("#star" + i).hover(function () {
            SetStartsGold(i);
            SetStartsGray(i + 1);
        }, function () {
            SetStartsGold(parseInt($("#note_form_value").val()));
            SetStartsGray(parseInt($("#note_form_value").val()) + 1);
        }
    );
}
//fonction de coloriage des etoiles de notation
function SetStartsGold(i) {
    $("#star" + i).css('color', "#ffdc0f");
    if (i > 0)
        SetStartsGold(i - 1);

}

function SetStartsGray(i) {
    $("#star" + i).css('color', "#808080");
    if (i < 5)
        SetStartsGray(i + 1);

}
// start the Stimulus application
// import './bootstrap';
// import "~bulma";