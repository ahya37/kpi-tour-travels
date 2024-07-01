$(document).ready(function () {

    const callApi = async (user) => {
        try {
            const response = await fetch(`/notifications/show/user/${user}`, {
                method: 'GET'
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            throw error;
        }
    };

    const getNotification = async (user) => {
        const response = await callApi(user);
        const count_notification = response.data.count_notification;

        if (count_notification > 0) {
            
            $('#countNotification').addClass("label label-primary");
            $('#countNotification').text(count_notification);
    
            let dropdownNotification = $('#dropdownNotification');
    
            dropdownNotification.empty();
            $.each(response.data.notifications, function (index, row) {
                const newList = `
                        <li>
                            <a href="/marketings/notifications/show/detail/user/${row.user_id}" class="dropdown-item">
                                <div>
                                    <h5>${row.title}</h5>
                                    <h6 class="float-right text-muted small">${row.detail}</h6>
                                </div>
                            </a>
                        </li>
                ` 
                dropdownNotification.append(newList);
            });
        }

    }

    getNotification(User);
   
});

