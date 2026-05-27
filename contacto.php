<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/header.php'; ?>

<section class="contacto-hero py-5">
  <div class="container text-center">
    <h1 class="display-3 fw-bold mb-4">Contacta con <span class="text-gradient">PetAdopta</span></h1>
    <p class="lead text-muted mx-auto" style="max-width: 600px;">
      ¿Tienes dudas? ¿Quieres información sobre algún perrito? Escríbenos y te responderemos lo antes posible.
    </p>
  </div>
</section>

<section class="contacto-form-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            ¡Mensaje enviado con éxito! Te contactaremos pronto.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <div class="card border-0 shadow-lg rounded-4 p-4 p-lg-5">
          <form action="procesar_contacto.php" method="post" class="contacto-form">
            
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">Nombre completo *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-person"></i>
                  </span>
                  <input type="text" class="form-control form-control-lg border-start-0" 
                         name="nombre" required placeholder="Tu nombre">
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="form-label fw-bold">Email *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input type="email" class="form-control form-control-lg border-start-0" 
                         name="email" required placeholder="tu@email.com">
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono <small class="text-muted">(opcional)</small></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-telephone"></i>
                  </span>
                  <input type="tel" class="form-control form-control-lg border-start-0" 
                         name="telefono" placeholder="Ej: 612 345 678">
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="form-label fw-bold">Asunto *</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-chat"></i>
                  </span>
                  <input type="text" class="form-control form-control-lg border-start-0" 
                         name="asunto" required placeholder="Motivo de tu mensaje">
                </div>
              </div>
              
              <div class="col-12">
                <label class="form-label fw-bold">Mensaje *</label>
                <textarea class="form-control" name="mensaje" rows="5" required 
                          placeholder="Escribe aquí tu mensaje..."></textarea>
              </div>
              
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="acepto" required>
                  <label class="form-check-label" for="acepto">
                    He leído y acepto la <a href="/legal/privacidad.php" target="_blank">Política de Privacidad</a> y consiento el tratamiento de mis datos para responder a mi consulta.
                  </label>
                </div>
              </div>
              
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill">
                  <i class="bi bi-send me-2"></i> Enviar mensaje
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="contacto-info-section py-5 bg-light">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="info-card text-center p-4 bg-white rounded-4 shadow-sm">
          <div class="info-icon mb-3">
            <i class="bi bi-geo-alt-fill fs-1 text-primary"></i>
          </div>
          <h5>Dirección</h5>
          <p class="text-muted">Calle Solidaridad, 45<br>12005 Castellón de la Plana</p>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="info-card text-center p-4 bg-white rounded-4 shadow-sm">
          <div class="info-icon mb-3">
            <i class="bi bi-telephone-fill fs-1 text-primary"></i>
          </div>
          <h5>Teléfono</h5>
          <p class="text-muted">964 123 456<br>654 123 456 (WhatsApp)</p>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="info-card text-center p-4 bg-white rounded-4 shadow-sm">
          <div class="info-icon mb-3">
            <i class="bi bi-envelope-fill fs-1 text-primary"></i>
          </div>
          <h5>Email</h5>
          <p class="text-muted">info@petadopta.org<br>adopta@petadopta.org</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/layout/footer.php'; ?>
