document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");
    const btnAdjuntar = document.getElementById("btnAdjuntar");
    const inputFile = document.getElementById("cv");
    const fileNameSpan = document.getElementById("fileName");

    // Abrir selector al clickear botón
    btnAdjuntar.addEventListener("click", () => {
        inputFile.click();
    });

    // Mostrar nombre del archivo seleccionado
    inputFile.addEventListener("change", () => {
        if (inputFile.files.length > 0) {
            fileNameSpan.textContent = inputFile.files[0].name;
        } else {
            fileNameSpan.textContent = "Ningún archivo seleccionado";
        }
    });

    // Validación y envío
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const file = inputFile.files[0];

        // Validar archivo (si existe)
        if (file) {
            const allowedTypes = ["application/pdf", "image/jpeg", "image/png"];
            const maxSize = 2 * 1024 * 1024; // 2 MB

            if (!allowedTypes.includes(file.type)) {
                alert("Formato no permitido. Solo PDF, JPG o PNG.");
                return;
            }
            if (file.size > maxSize) {
                alert("El archivo supera los 2MB.");
                return;
            }
        }

        // reCAPTCHA (comentado)
        /*
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            alert("Por favor, completa el reCAPTCHA.");
            return;
        }
        */

        // Preparar datos
        const formData = new FormData(form);

        try {
            const response = await fetch("enviar.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert("Mensaje enviado correctamente.");
                form.reset();
                fileNameSpan.textContent = "Ningún archivo seleccionado";
                // grecaptcha.reset(); // descomentar cuando se use reCAPTCHA
            } else {
                alert("Error: " + result.error);
            }
        } catch (error) {
            console.error(error);
            alert("Hubo un error al enviar el formulario.");
        }
    });
});
