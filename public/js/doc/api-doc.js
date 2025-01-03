document.addEventListener("DOMContentLoaded", function () {
    const host = window.location.origin;

    window.ui = SwaggerUIBundle({
        url: `${host}/api/internal/documentation`,
        dom_id: '#swagger-ui',
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
        ],
        layout: "StandaloneLayout"
      });
});
