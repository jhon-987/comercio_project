// Validación de formularios
document.addEventListener('DOMContentLoaded', function() {
    // Validar formulario de perfil
    const perfilForm = document.querySelector('form[action*="actualizarPerfil"]');
    if (perfilForm) {
        perfilForm.addEventListener('submit', function(e) {
            const nombre = document.querySelector('input[name="nombre"]').value;
            const correo = document.querySelector('input[name="correo"]').value;
            const telefono = document.querySelector('input[name="telefono"]').value;
            
            if (nombre.length < 3) {
                e.preventDefault();
                alert('El nombre debe tener al menos 3 caracteres');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(correo)) {
                e.preventDefault();
                alert('Por favor ingresa un correo electrónico válido');
                return;
            }
            
            const phoneRegex = /^[0-9]{9}$/;
            if (!phoneRegex.test(telefono)) {
                e.preventDefault();
                alert('El teléfono debe tener 9 dígitos numéricos');
                return;
            }
        });
    }
    
    // Validar formulario de pedido
    const pedidoForm = document.querySelector('form[action*="confirmarPedido"]');
    if (pedidoForm) {
        pedidoForm.addEventListener('submit', function(e) {
            const nombre = document.querySelector('input[name="nombre"]').value;
            const direccion = document.querySelector('input[name="direccion"]').value;
            
            if (nombre.length < 3) {
                e.preventDefault();
                alert('El nombre completo es requerido');
                return;
            }
            
            if (direccion.length < 5) {
                e.preventDefault();
                alert('La dirección de envío es requerida');
                return;
            }
        });
    }
    
    // Efectos visuales para botones
    const buttons = document.querySelectorAll('button, .btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Mostrar alerta cuando se agrega producto al carrito
    const addCartForms = document.querySelectorAll('form[action*="agregarCarrito"]');
    addCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const productName = this.querySelector('input[name="nombre"]').value;
            setTimeout(() => {
                alert(`✅ ${productName} agregado al carrito exitosamente!`);
            }, 100);
        });
    });
});

// Función para calcular subtotal en tiempo real (para futuras mejoras)
function calcularSubtotal(cantidad, precio, elementId) {
    const subtotal = cantidad * precio;
    document.getElementById(elementId).textContent = subtotal.toFixed(2);
}