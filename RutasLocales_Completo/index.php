<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas Locales - Explorar</title>
    <!-- Iconos profesionales gratuitos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-theme">
    
    <!-- Barra de Navegación Superior -->
    <header class="navbar">
        <div class="logo">
            <h2><i class="fa-solid fa-mountain-sun"></i> RutasLocales</h2>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Panel de Control</a>
            <a href="login.php" class="btn-login">Iniciar Sesión</a>
        </div>
    </header>

    <!-- Barra de Búsqueda -->
    <section class="search-section">
        <div class="search-bar">
            <div class="search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="buscador" placeholder="Buscar destino, ciudad o atracción...">
            </div>
            <button class="btn-search">Buscar</button>
        </div>
    </section>

    <!-- Contenido Principal: Dos Columnas -->
    <main class="layout-grid">
        
        <!-- Columna Izquierda: Lista de Experiencias -->
        <div class="experiencias-lista" id="lista-experiencias">
            <p class="results-text">Mostrando resultados recomendados para ti</p>

            <!-- Tarjeta 1 -->
            <div class="experiencia-card" data-titulo="Páramo de Sumapaz">
                <div class="card-img" style="background-image: url('https://images.unsplash.com/photo-1590479773265-7464e5d48118?auto=format&fit=crop&q=80&w=600');">
                    <div class="card-badge">Destacado</div>
                </div>
                <div class="card-content">
                    <h3>Caminata Páramo de Sumapaz</h3>
                    <p class="location"><i class="fa-solid fa-location-dot"></i> Bogotá, Colombia</p>
                    <div class="amenities">
                        <span><i class="fa-solid fa-person-hiking"></i> 5 horas</span>
                        <span><i class="fa-solid fa-leaf"></i> Ecoturismo</span>
                    </div>
                    <div class="card-footer">
                        <span class="price">$50.000 COP <small>/ persona</small></span>
                        <a href="reserva.php" class="btn-reservar">Ver y Reservar</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2 -->
            <div class="experiencia-card" data-titulo="Salto del Tequendama">
                <div class="card-img" style="background-image: url('https://images.unsplash.com/photo-1549891823-c28bc74a6f7b?auto=format&fit=crop&q=80&w=600');">
                </div>
                <div class="card-content">
                    <h3>Salto del Tequendama y Museo</h3>
                    <p class="location"><i class="fa-solid fa-location-dot"></i> Soacha, Cundinamarca</p>
                    <div class="amenities">
                        <span><i class="fa-solid fa-camera"></i> Paisajismo</span>
                        <span><i class="fa-solid fa-landmark"></i> Historia</span>
                    </div>
                    <div class="card-footer">
                        <span class="price">$35.000 COP <small>/ persona</small></span>
                        <a href="reserva.php" class="btn-reservar">Ver y Reservar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Mapa Interactivo -->
        <div class="mapa-container">
            <div class="mapa-placeholder">
                <i class="fa-solid fa-map-location-dot"></i>
                <p>Mapa interactivo de la zona</p>
                <small>Explora las ubicaciones</small>
            </div>
        </div>
    </main>

    <!-- Footer Principal -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section brand">
                <h2><i class="fa-solid fa-mountain-sun"></i> RutasLocales</h2>
                <p>Conectando viajeros con experiencias ecológicas y guías locales en Colombia. Descubre tu próxima aventura.</p>
            </div>
            
            <div class="footer-section links">
                <h3>Explorar</h3>
                <a href="index.php">Destinos</a>
                <a href="#">Cómo funciona</a>
                <a href="#">Términos y Condiciones</a>
                <a href="registro.php">Quiero ser Guía</a>
            </div>
            
            <div class="footer-section contact">
                <h3>Contacto</h3>
                <p><i class="fa-solid fa-envelope"></i> soporte@rutaslocales.com</p>
                <p><i class="fa-solid fa-phone"></i> +57 300 000 0000</p>
                <p><i class="fa-solid fa-location-dot"></i> Bogotá, Colombia</p>
            </div>
            
            <div class="footer-section social">
                <h3>Síguenos</h3>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> RutasLocales. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        const buscador = document.getElementById('buscador');
        const cards = document.querySelectorAll('.experiencia-card');

        buscador.addEventListener('keyup', (e) => {
            const texto = e.target.value.toLowerCase();
            cards.forEach(card => {
                const titulo = card.getAttribute('data-titulo').toLowerCase();
                if(titulo.includes(texto)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>