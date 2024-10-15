<?php
    include_once('config.php');
    include_once('template.php');

    session_start();

    function get_endereco($cep)
    {
        $cep = preg_replace("/[^0-9]/", "", $cep);
        $url = "http://viacep.com.br/ws/$cep/xml";

        $xml = simplexml_load_file($url);
        return $xml;
    }

    // verifica se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['especialidade']) || count($_POST['especialidade']) < 2) {
            $_SESSION['message'] = "Por favor, selecione pelo menos duas especialidades.";
            $_SESSION['message_type'] = "error";
        } else {
            $nome = $_POST["nome"];
            $crm = $_POST["crm"];
            $telefone = $_POST["telefone"];
            $especialidade = $_POST["especialidade"];
            $logradouro = $_POST["logradouro"];
            $numero = $_POST["numero"];
            $bairro = $_POST["bairro"];
            $cep = $_POST["cep"];
            $cidade = $_POST["cidade"];
            $uf = $_POST["uf"];

            if (!$conn) {
                die("Conexão com o banco de dados falhou: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("INSERT INTO medicos (nome, crm, telefone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $crm, $telefone);
            $res_medico = $stmt->execute();

            if ($res_medico) {
                $id_medico = $conn->insert_id;

                $stmt = $conn->prepare("INSERT INTO enderecos (logradouro, numero, bairro, cep, cidade, uf) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $logradouro, $numero, $bairro, $cep, $cidade, $uf);
                $res_endereco = $stmt->execute();

                if ($res_endereco) {
                    $id_endereco = $conn->insert_id;

                    $stmt = $conn->prepare("INSERT INTO medico_endereco (idMedico, idEndereco) VALUES (?, ?)");
                    $stmt->bind_param("ii", $id_medico, $id_endereco);
                    $stmt->execute();

                    foreach ($especialidade as $id_especialidade) {
                        $stmt = $conn->prepare("INSERT INTO medico_especialidade (idMedico, idEspecialidade) 
                                                VALUES (?, ?)");
                        $stmt->bind_param("ii", $id_medico, $id_especialidade);
                        $stmt->execute();
                    }

                    $_SESSION['message'] = "Médico cadastrado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Erro ao cadastrar endereço: " . $stmt->error;
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Erro ao cadastrar médico: " . $stmt->error;
                $_SESSION['message_type'] = "error";
            }
        }
    }

    // verifica se há mensagens na sessão e as exibe
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . ($_SESSION['message_type'] === 'error' ? 'danger' : 'success') . '">';
        echo $_SESSION['message'];
        echo '</div>';
        
        // Limpa a mensagem após exibi-la
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<div class="container mt-5">
    <div class="row d-flex justify-content-center align-items-center" style="min-height: 16rem;">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Cadastrar Médico</h2>
                </div>
                <div class="card-body">
                    <form action="register.php" method="POST" class="row g-3">
                        <input type="hidden" name="acao" value="cadastrar">
                        
                        <div class="mb-3 col-9">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>    

                        <div class="mb-3 col-md-3">
                            <label>CRM</label>
                            <input type="text" name="crm" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>Telefone</label>
                            <input type="text" id="telefone" name="telefone" maxlength="14" placeholder="(00) 00000-0000" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Especialidades</label>
                            <select multiple name="especialidade[]" id="especialidade" class="form-control selectpicker" data-live-search="true">
                                <?php
                                    $query = "SELECT id_especialidade, especialidade FROM especialidades";
                                    $result = $conn->query($query);
                                    
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id_especialidade'] . '">' . $row['especialidade'] . '</option>';
                                    }
                                ?>
                            </select>
                            <input type="hidden" name="hidden_framework" id="hidden_framework" />
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Logradouro</label>
                            <input type="text" name="logradouro" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>Número</label>
                            <input type="text" name="numero" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Bairro</label>
                            <input type="text" name="bairro" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>CEP</label>
                            <input type="text" id="cep" name="cep" maxlength="9" placeholder="00000-000" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-9">
                            <label>Cidade</label>
                            <input type="text" name="cidade" class="form-control" required>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label>UF</label>
                            <input type="text" name="uf" class="form-control" maxlength="2" required>
                        </div>

                        <div class="mb-3 col-12">
                            <button type="submit" class="btn btn-primary w-100">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
$(document).ready(function(){


    // inicializa o seletpicker, usado pra selecionar as especialidades 
    $('.selectpicker').selectpicker();

    $('#especialidade').change(function(){
        $('#hidden_framework').val($('#especialidade').val());
    });


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