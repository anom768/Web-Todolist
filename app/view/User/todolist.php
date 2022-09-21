<div class="container col-xl-10 col-xxl-8 px-4 py-5">

    <?php if (isset($model["error"])) { ?>
        <div class="row">
        <div class="alert alert-danger" role="alert">
            <?= $model["error"] ?>
        </div>
    </div>
    <?php }?>
    <div class="row">
        <form method="post" action="/">
            <button class="w-15 btn btn-lg btn-danger" type="submit">Back</button>
        </form>
    </div>
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Todolist</h1>
            <!-- <p class="col-lg-10 fs-4">by <a target="_blank" href="https://www.programmerzamannow.com/">Programmer Zaman
                    Now</a></p> -->
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/users/todolist">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="todo" placeholder="todo">
                    <label for="todo">Todo</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Add Todo</button>
            </form>
        </div>
    </div>
    <div class="row align-items-right g-lg-5 py-5">
        <div class="mx-auto">
            
            <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Todo</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    if (isset($model["todolist"])) {
                        foreach ($model["todolist"] as $row) { ?>
                            <tr>
                                <th scope="row"><?= $row["id"] ?></th>
                                <td><b><?= $row["todolist"] ?></b></td>
                                <form id="deleteForm" action="/todolist/remove?id=<?= $row["id"] ?>" method="post" style="display: none">
                                <td>
                                    <button class="w-100 btn btn-lg btn-danger" type="submit" onclick="return confirm('Are you sure want delete this todo ?')">Remove</button>
                                </td>
                                </form>
                            </tr> <?php
                        }
                    }
                    ?>
                
            </tbody>
        </table>
        </div>
    </div>
</div>