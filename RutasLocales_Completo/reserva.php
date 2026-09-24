<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Experiencia</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contenedor-formulario">
        <h2>Reservar Experiencia</h2>
        <form id="formReserva" action="procesar_reserva.php" method="POST">
            <input type="hidden" name="id_experiencia" value="1">
            <input type="hidden" name="id_turista" value="2">
            
            <label for="cantidad">Cantidad de Personas:</label>
            <input type="number" id="cantidad" name="cantidad" min="1" required>
            
            <label for="fecha">Fecha de la reserva:</label>
            <input type="date" id="fecha" name="fecha" required>

            <p>Total estimado: $<span id="totalPagar">0</span></p>

            <button type="submit">Confirmar Reserva</button>
        </form>
    </div>

    <script>
        const precioPorPersona = 50000;
        const inputCantidad = document.getElementById('cantidad');
        const spanTotal = document.getElementById('totalPagar');
        const form = document.getElementById('formReserva');

        inputCantidad.addEventListener('input', () => {
            let total = inputCantidad.value * precioPorPersona;
            spanTotal.textContent = total.toLocaleString();
        });

        form.addEventListener('submit', (e) => {
            if(inputCantidad.value <= 0) {
                e.preventDefault(); 
                alert("Debes seleccionar al menos una persona.");
            }
        });
    </script>
</body>
</html>