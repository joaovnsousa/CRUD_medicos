<?php
    include_once('template.php');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="text-center">Pesquisar Médico</h2>
                    <form action="" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label for="search_type" class="form-label">Buscar por:</label>
                            <select name="search_type" id="search_type" class="form-select" required>
                                <option value="nome">Nome</option>
                                <option value="crm">CRM</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="search_value" class="form-label">Digite a informação:</label>
                            <input type="text" name="search_value" id="search_value" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    include_once('config.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_type']) && isset($_POST['search_value'])) {
    
        $search_type = $_POST['search_type'];
        $search_value = $_POST['search_value'];
    
        if ($search_type === 'nome') {
            $query = "
                SELECT medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, enderecos.logradouro, enderecos.numero, enderecos.bairro, enderecos.cep, 
                       enderecos.cidade, enderecos.uf, 
                       GROUP_CONCAT(especialidades.especialidade ORDER BY especialidades.especialidade SEPARATOR ', ') AS especialidades
                FROM medicos
                JOIN medico_endereco ON medicos.id_medico = medico_endereco.idMedico
                JOIN enderecos ON medico_endereco.idEndereco = enderecos.idendereco
                JOIN medico_especialidade ON medicos.id_medico = medico_especialidade.idMedico
                JOIN especialidades ON medico_especialidade.idEspecialidade = especialidades.id_especialidade
                WHERE medicos.nome LIKE ?
                GROUP BY medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, enderecos.logradouro, enderecos.numero, enderecos.bairro, enderecos.cep, enderecos.cidade, enderecos.uf;
            ";
            $search_value = "%" . $search_value . "%";
        } elseif ($search_type === 'crm') {
            $query = "
                SELECT medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, enderecos.logradouro, enderecos.numero, enderecos.bairro, enderecos.cep, 
                       enderecos.cidade, enderecos.uf, 
                       GROUP_CONCAT(especialidades.especialidade ORDER BY especialidades.especialidade SEPARATOR ', ') AS especialidades
                FROM medicos
                JOIN medico_endereco ON medicos.id_medico = medico_endereco.idMedico
                JOIN enderecos ON medico_endereco.idEndereco = enderecos.idendereco
                JOIN medico_especialidade ON medicos.id_medico = medico_especialidade.idMedico
                JOIN especialidades ON medico_especialidade.idEspecialidade = especialidades.id_especialidade
                WHERE medicos.crm = ?
                GROUP BY medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, enderecos.logradouro, enderecos.numero, enderecos.bairro, enderecos.cep, enderecos.cidade, enderecos.uf;
            ";
        } else {
            echo "<div class='alert alert-danger'>Tipo de pesquisa inválido.</div>";
            exit;
        }

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("s", $search_value);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                echo "<h2 class='mt-4'>Resultados da pesquisa:</h2>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped table-hover'>";
                echo "<thead class='table-dark'>";
                echo "<tr>";
                echo "<th>Nome</th>";
                echo "<th>CRM</th>";
                echo "<th>Telefone</th>";
                echo "<th>Logradouro</th>";
                echo "<th>Número</th>";
                echo "<th>Bairro</th>";
                echo "<th>CEP</th>";
                echo "<th>Cidade</th>";
                echo "<th>UF</th>";
                echo "<th>Especialidades</th>";
                echo "<th>Ações</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['crm'] . "</td>";
                    echo "<td>" . $row['telefone'] . "</td>";
                    echo "<td>" . $row['logradouro'] . "</td>";
                    echo "<td>" . $row['numero'] . "</td>";
                    echo "<td>" . $row['bairro'] . "</td>";
                    echo "<td>" . $row['cep'] . "</td>";
                    echo "<td>" . $row['cidade'] . "</td>";
                    echo "<td>" . $row['uf'] . "</td>";
                    echo "<td>" . $row['especialidades'] . "</td>";
                    echo "<td>
                            <button onclick=\"location.href='edit_users.php?id={$row['id_medico']}';\" class='btn btn-success btn-sm'>Editar</button>
                            <a href='select_users.php?acao=excluir&id={$row['id_medico']}' onclick=\"return confirm('Tem certeza que deseja excluir este médico?');\" class='btn btn-danger btn-sm'>Excluir</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p class='alert alert-warning mt-4'>Nenhum médico encontrado.</p>";
            }
        } else {
            echo "<p class='alert alert-danger'>Erro na execução da query: " . $conn->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
