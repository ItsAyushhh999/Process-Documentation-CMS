<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
  <!-- {{-- <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.core.css"> --}} -->
  <!-- <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet"> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  <!-- <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/fixedheader/3.2.3/js/dataTables.fixedHeader.min.js"></script> -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>

    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        var key = "{{ config('broadcasting.connections.pusher.key') }}";
        const pusher = new Pusher(key, {
            cluster: 'ap2'
        });
    </script>
    @auth
        <script>
            const authUserEmail = "{{ Auth::user()->email }}"
            const beamsClient = new PusherPushNotifications.Client({
                instanceId: "{{ config('services.pusher.INSTANCE_ID') }}",
            })

            const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
                url: "{{ route('beams.auth') }}",
            });

            beamsClient.getUserId().then($id => console.log('userId',$id));

            beamsClient.start()
                .then(() => beamsClient.getUserId())
                .then($id => {
                    if (!$id) {
                        return beamsClient.setUserId(authUserEmail, beamsTokenProvider);
                    }
                })
                .catch(console.error);
        </script>
    @endauth

  @vite(['resources/css/app.css', 'resources/js/laravelApp.js', 'resources/js/script.js'])

  <title>Task Management System</title>

  <style>
    html {
      font-size: 14px;
    }

    @media screen and (max-width: 768px) {
      html {
        font-size: 16px;
      }
    }
  </style>
</head>

<body class="dark:bg-slate-900 dark:border-gray-200 bg-slate-50">
  <div class="relative">
      <main class=" w-full h-full">
        <!-- @yield('header') -->
        @yield('content')
      </main>
  </div>


  <script type="text/javascript">

    $(document).ready(function() {
        //Select branch for storing Pipeline Name
        function handleBranchDisplay(branch) {
        switch (branch) {
            case "stagging":
                $('#staging_pipeline').removeClass('hidden');
                $('#production_Pipeline').addClass('hidden');
                $('#developmet_pipeline').addClass('hidden');
                $('#development_pipeline_name').val('');
                $('#production_Pipeline_name').val('');
                break;
            case "production":
                $('#production_Pipeline').removeClass('hidden');
                $('#staging_pipeline').addClass('hidden');
                $('#developmet_pipeline').addClass('hidden');
                $('#development_pipeline_name').val('');
                $('#staging_pipeline_name').val('');
                break;
            case "development":
                $('#developmet_pipeline').removeClass('hidden');
                $('#production_Pipeline').addClass('hidden');
                $('#staging_pipeline').addClass('hidden');
                $('#staging_pipeline_name').val('');
                $('#production_Pipeline_name').val('');
                break;
            default:
                $('#staging_pipeline').addClass('hidden');
                $('#developmet_pipeline').addClass('hidden');
                $('#production_Pipeline').addClass('hidden');
                $('#development_pipeline_name').val('');
                $('#staging_pipeline_name').val('');
                $('#production_Pipeline_name').val('');
                break;
        }
    }

    // Select branch for storing Pipeline Name
    $('#project_branch').on('change', function() {
        let branch = $(this).val();
        handleBranchDisplay(branch);
    });

    //Displaying the pipeline according to the selected branch
    let initialBranch = $('#project_branch').val();
         handleBranchDisplay(initialBranch);
    });

    // $(document).ready(function () {
    //     $('#userAction').on('change', function () {
    //         var selectedOption = $(this).val();
    //         if (selectedOption === 'profile') {
    //             window.location.href = '/profile';
    //         } else if (selectedOption === 'logout') {
    //             window.location.href = '/logout';
    //         }
    //     });
    // });


</script>
</body>

</html>
