<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<?php
    include_once('template.php');
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Médicos Cadastrados</h1>

    <?php
        include_once('config.php');

        // verifica se há uma ação para excluir médico
        if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
            $id_medico = $_GET['id'];

            // exclui especialidades associadas ao médico
            $stmt = $conn->prepare("DELETE FROM medico_especialidade WHERE idMedico = ?");
            $stmt->bind_param("i", $id_medico);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM medico_endereco WHERE idMedico = ?");
            $stmt->bind_param("i", $id_medico);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM medicos WHERE id_medico = ?");
            $stmt->bind_param("i", $id_medico);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<div class='alert alert-success'>Médico excluído com sucesso!</div>";
            } else {
                echo "<div class='alert alert-danger'>Erro ao excluir médico.</div>";
            }
        }

        $sql = "SELECT medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, enderecos.logradouro, enderecos.numero, enderecos.cep, enderecos.cidade, enderecos.uf, 
            GROUP_CONCAT(especialidades.especialidade ORDER BY especialidades.especialidade SEPARATOR ', ') AS especialidades 
            FROM medicos 
            JOIN medico_endereco ON medicos.id_medico = medico_endereco.idMedico
            JOIN enderecos ON medico_endereco.idEndereco = enderecos.idendereco
            JOIN medico_especialidade ON medicos.id_medico = medico_especialidade.idMedico
            JOIN especialidades ON medico_especialidade.idEspecialidade = especialidades.id_especialidade
            GROUP BY medicos.id_medico, medicos.nome, medicos.crm, enderecos.logradouro, enderecos.numero, enderecos.cep, enderecos.cidade, enderecos.uf";

        $res = $conn->query($sql);
        $qtd = $res->num_rows;

        if ($qtd > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-hover table-striped table-bordered'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Nome</th>";
            echo "<th>CRM</th>";
            echo "<th>Telefone</th>"; 
            echo "<th>Logradouro</th>"; 
            echo "<th>N°</th>"; 
            echo "<th>CEP</th>"; 
            echo "<th>Cidade</th>";
            echo "<th>UF</th>";
            echo "<th>Especialidades</th>";
            echo "<th>Ações</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = $res->fetch_object()) {
                echo "<tr>";
                echo "<td>{$row->nome}</td>";
                echo "<td>{$row->crm}</td>";
                echo "<td>{$row->telefone}</td>";
                echo "<td>{$row->logradouro}</td>";
                echo "<td>{$row->numero}</td>";
                echo "<td>{$row->cep}</td>";
                echo "<td>{$row->cidade}</td>";
                echo "<td>{$row->uf}</td>";
                echo "<td>{$row->especialidades}</td>";
                echo "<td>
                        <button onclick=\"location.href='edit_users.php?id={$row->id_medico}';\" class='btn btn-success btn-sm'>Editar</button>
                        <a href='select_users.php?acao=excluir&id={$row->id_medico}' onclick=\"return confirm('Tem certeza que deseja excluir este médico?');\" class='btn btn-danger btn-sm'>Excluir</a>
                      </td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p class='text-center'>Nenhum registro encontrado.</p>";
        }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
