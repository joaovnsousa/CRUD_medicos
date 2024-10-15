<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="/images/icon.svg" alt="Logo" style="height: 2.5rem;">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Página Inicial</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href='register.php'>Cadastro</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" type="button" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Listar
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href='select_users.php'>Todos os Médicos</a></li>
            <li><a class="dropdown-item" href='search_users.php'>Procurar Médico</a></li>
          </ul>
        </li>
      </ul>
    
    </div>
  </div>
  </nav>