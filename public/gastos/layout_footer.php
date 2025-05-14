<?php
// layout_footer.php
// Este archivo cierra las etiquetas HTML principales y añade JavaScript para la funcionalidad del layout.
?>
            </main> </div> </div> <script>
        // Script para manejar la funcionalidad del menú lateral (sidebar) en móviles.
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('mobileMenuButton'); // Botón para mostrar/ocultar el menú en móviles
            const sidebar = document.querySelector('.sidebar'); // El elemento de la barra lateral

            // Verifica que ambos elementos existan en el DOM para evitar errores.
            if (mobileMenuButton && sidebar) {
                // Añade un event listener al botón del menú móvil.
                mobileMenuButton.addEventListener('click', () => {
                    // Alterna la clase '-translate-x-full' para mostrar u ocultar la barra lateral.
                    // '-translate-x-full' la mueve completamente fuera de la pantalla a la izquierda.
                    // Quitar la clase la devuelve a su posición original (translate-x-0).
                    sidebar.classList.toggle('-translate-x-full');
                });
            }

            // Opcional: Cerrar el menú lateral si se hace clic fuera de él en pantallas móviles.
            document.addEventListener('click', (event) => {
                // Solo se ejecuta en pantallas menores a 768px (el breakpoint 'md' de Tailwind)
                if (window.innerWidth < 768 && sidebar && mobileMenuButton) {
                    // Comprueba si el clic NO fue dentro de la barra lateral NI en el botón del menú,
                    // y si la barra lateral NO está ya oculta.
                    if (!sidebar.contains(event.target) && 
                        !mobileMenuButton.contains(event.target) && 
                        !sidebar.classList.contains('-translate-x-full')) {
                        
                        sidebar.classList.add('-translate-x-full'); // Oculta la barra lateral
                    }
                }
            });
        });
    </script>
</body>
</html>
