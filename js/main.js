// ver mas fotos
function verMasFotos(id) {
    window.location.href = 'perrito_detalle.php?id=' + id;
}

// previsualizar fotos antes de subir
function previsualizarFotos(input) {
    var preview = document.getElementById('vistaPrevia');
    if(!preview) return;
    
    preview.innerHTML = '';
    
    if(input.files.length > 5) {
        alert('Maximo 5 fotos');
        input.value = '';
        return;
    }
    
    for(var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        
        if(file.size > 5 * 1024 * 1024) {
            alert('Foto muy grande, maximo 5MB');
            continue;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.margin = '5px';
            img.style.borderRadius = '8px';
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
}
