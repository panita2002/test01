function saveDesign() {
    unlayer.exportHtml(function(data) {
        console.log('HTML:', data.html);
        console.log('Design JSON:', data.design);
    });
}
