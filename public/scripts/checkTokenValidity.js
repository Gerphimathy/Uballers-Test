function checkTokenValidity(callback){
    if(localStorage.getItem("token") === ""||localStorage.getItem("token") == null) callback(403);
    else try {
        let xhttp = new XMLHttpRequest();
        xhttp.open("GET", "/auth?token="+localStorage.getItem("token"), false);
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);

                if (parsedResponse.success === true){
                    if (parsedResponse.info.isvalid === true)callback(200);
                    else callback(403);
                } else callback(500);
            }
        };
        xhttp.send();
    } catch (error) {
        console.error(error);
        callback(500);
    }
}

export{ checkTokenValidity }