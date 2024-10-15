<?php
    include_once('config.php');
    session_start();
    include_once('template.php');

    // verifica se o ID do médico está presente
    if (isset($_GET['id'])) {
        $id_medico = $_GET['id'];
        
        $query = "SELECT medicos.id_medico, medicos.nome, medicos.crm, medicos.telefone, 
            enderecos.logradouro, enderecos.numero, enderecos.bairro, 
            enderecos.cep, enderecos.cidade, enderecos.uf, 
            GROUP_CONCAT(especialidades.especialidade) as especialidade 
            FROM medicos 
            LEFT JOIN medico_endereco ON medicos.id_medico = medico_endereco.idMedico
            LEFT JOIN enderecos ON medico_endereco.idEndereco = enderecos.idEndereco
            LEFT JOIN medico_especialidade ON medicos.id_medico = medico_especialidade.idMedico
            LEFT JOIN especialidades ON medico_especialidade.idEspecialidade = especialidades.id_especialidade
            WHERE medicos.id_medico = $id_medico
            GROUP BY medicos.id_medico, enderecos.logradouro, enderecos.numero, enderecos.bairro, 
                    enderecos.cep, enderecos.cidade, enderecos.uf";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
    }

    // se o formulário for enviado, realizar a atualização
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_medico = $_POST['id_medico'];
        $nome = $_POST['nome'];
        $crm = $_POST['crm'];
        $telefone = $_POST['telefone'];

        // valida se foram selecionadas pelo menos duas especialidades
        if (isset($_POST['especialidade']) && count($_POST['especialidade']) >= 2) {
            $query = "UPDATE medicos SET nome='$nome', crm='$crm', telefone='$telefone' WHERE id_medico='$id_medico'";
            if ($conn->query($query)) {
                // atualiza endereço
                $logradouro = $_POST['logradouro'];
                $numero = $_POST['numero'];
                $bairro = $_POST['bairro'];
                $cep = $_POST['cep'];
                $cidade = $_POST['cidade'];
                $uf = $_POST['uf'];

                $query_endereco = "UPDATE enderecos 
                                    JOIN medico_endereco ON enderecos.idEndereco = medico_endereco.idEndereco
                                    SET logradouro='$logradouro', numero='$numero', bairro='$bairro', cep='$cep', cidade='$cidade', uf='$uf'
                                    WHERE medico_endereco.idMedico='$id_medico'";
                $conn->query($query_endereco);

                // atualiza especialidades do médico
                $conn->query("DELETE FROM medico_especialidade WHERE idMedico='$id_medico'");
                foreach ($_POST['especialidade'] as $id_especialidade) {
                    $stmt = $conn->prepare("INSERT INTO medico_especialidade (idMedico, idEspecialidade) VALUES (?, ?)");
                    $stmt->bind_param("ii", $id_medico, $id_especialidade);
                    $stmt->execute();
                }

                $_SESSION['sucesso'] = "Médico atualizado com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar médico!";
            }
        } else {
            $_SESSION['erro'] = "Selecione pelo menos duas especialidades.";
        }

    }

    if (isset($_SESSION['sucesso'])) {
        echo '<div class="alert alert-success">' . $_SESSION['sucesso'] . '</div>';
        unset($_SESSION['sucesso']);
    }

    if (isset($_SESSION['erro'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['erro'] . '</div>';
        unset($_SESSION['erro']);
    }
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<div class="container mt-5">
    <div class="row d-flex justify-content-center align-items-center" style="min-height: 16rem;">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Editar Usuário</h2>
                </div>
                <div class="card-body">
                    <form action="edit_users.php?id=<?php echo $id_medico; ?>" method="POST" class="row g-3">
                        <input type="hidden" name="id_medico" value="<?php echo $row['id_medico']; ?>">

                        <div class="mb-3 col-9">
                            <label>Nome</label>
                            <input type="text" name="nome" value="<?php echo $row['nome']; ?>" class="form-control" required>
                        </div>    

                        <div class="mb-3 col-md-3">
                            <label>CRM</label>
                            <input type="text" name="crm" value="<?php echo $row['crm']; ?>" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>Telefone</label>
                            <input type="tel" name="telefone" value="<?php echo $row['telefone']; ?>" maxlength="14" placeholder="(00) 00000-0000" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Especialidades</label>
                            <select multiple name="especialidade[]" id="especialidade" class="form-control selectpicker" data-live-search="true" required>
                                <?php
                                    $query_especialidades = "SELECT id_especialidade, especialidade FROM especialidades";
                                    $result_especialidades = $conn->query($query_especialidades);

                                    while ($row_especialidade = $result_especialidades->fetch_assoc()) {
                                        $selected = in_array($row_especialidade['especialidade'], explode(", ", $row['especialidade'])) ? 'selected' : '';
                                        echo '<option value="' . $row_especialidade['id_especialidade'] . '" ' . $selected . '>' . $row_especialidade['especialidade'] . '</option>';
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Logradouro</label>
                            <input type="text" name="logradouro" value="<?php echo $row['logradouro']; ?>" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>Número</label>
                            <input type="text" name="numero" value="<?php echo $row['numero']; ?>" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Bairro</label>
                            <input type="text" name="bairro" value="<?php echo $row['bairro']; ?>" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>CEP</label>
                            <input type="text" name="cep" value="<?php echo $row['cep']; ?>" maxlength="9" placeholder="00000-000" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Cidade</label>
                            <input type="text" name="cidade" value="<?php echo $row['cidade']; ?>" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>UF</label>
                            <input type="text" name="uf" value="<?php echo $row['uf']; ?>" class="form-control" maxlength="2" required>
                        </div>

                        <div class="mb-3 col-12">
                            <button type="submit" class="btn btn-primary w-100">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jEgtM" crossorigin="anonymous"></script>

<script>
$(document).ready(function(){
    // scripts 
    $('.selectpicker').selectpicker();

    $('input[name="cep"]').on('change', function() {
        var cep = $(this).val();

        // requisição da api viacep
        $.ajax({
            url: 'https://viacep.com.br/ws/' + cep + '/json/',
            dataType: 'json',
            success: function(data) {
                if (!data.erro) {
                    // preenche o endereço automaticamente
                    $('input[name="logradouro"]').val(data.logradouro);
                    $('input[name="bairro"]').val(data.bairro);
                    $('input[name="cidade"]').val(data.localidade);
                    $('input[name="uf"]').val(data.uf);
                } else {
                    alert('CEP inválido!');
                }
            },
            error: function() {
                alert('Erro ao buscar o CEP.');
            }
        });
    });
});
</script>