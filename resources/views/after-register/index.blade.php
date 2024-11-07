<x-app-layout>
    <div class="row">
        <h1>É novo por aqui?</h1>
        <p>Comece cadastrando um novo pet:</p>
        <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12">
                <div class="form-floating mb-3">
                    <input type="text" name="name" class="form-control" id="floatingInput">
                    <label for="floatingInput">Nome do pet</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="breed" class="form-control" id="floatingInput">
                    <label for="floatingInput">Raça</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="age" class="form-control" id="floatingInput">
                    <label for="floatingInput">Idade</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="size" class="form-control" id="floatingInput">
                    <label for="floatingInput">Porte</label>
                </div>
                <div class="form-group mb-3">
                    <label for="floatingInput">Pedigree</label>
                    <input type="checkbox" name="pedigree" class="form-checkbox">
                </div>
                <div class="form-floating mb-3">
                    <textarea name="description" class="form-control" id="floatingTextarea"></textarea>
                    <label for="floatingTextarea">Observação</label>
                </div>
                <input class="btn btn-primary" type="submit" value="Cadastrar">
            </div>
        </form>
    </div>

</x-app-layout>
