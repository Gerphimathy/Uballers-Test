import {checkTokenValidity} from "./checkTokenValidity.js";

const mainSignedIn = document.getElementById("signed-main");
const mainNotSignedIn = document.getElementById("unsigned-main");

const loginForm = document.getElementById("login-form");
const loginLogin = document.getElementById("login-login");
const loginPass = document.getElementById("login-pass");
const loginConfirm = document.getElementById("login-confirm");


const signupForm = document.getElementById("signup-form");
const signupLogin1 = document.getElementById("signup-login-1");
const signupLogin2 = document.getElementById("signup-login-2");
const signupPass = document.getElementById("signup-pass");
const signupDate = document.getElementById("signup-date");
const signupFirst = document.getElementById("signup-firstname");
const signupLast = document.getElementById("signup-lastname");
const signupConfirm = document.getElementById("signup-confirm");


const errorDisplay = document.getElementById("error-display");


loginConfirm.onclick = function (){
    login(loginLogin.value, loginPass.value);
};


signupConfirm.onclick = function () {
    if (signupLogin1.value === signupLogin2.value){
        try {
            let xhttp = new XMLHttpRequest();
            xhttp.open("PUT", "/login", false);
            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    switch (xhttp.status){
                        case 200:
                            login(signupLogin1.value, signupPass.value);
                            break;
                        case 500:
                            errorDisplay.style.display = "block";
                            errorDisplay.innerHTML="Erreur Interne - Veuillez réessayer plus tard";
                            break;
                        case 400:
                            const response = this.responseText;
                            const parsedResponse = JSON.parse(response);
                            handle400(parsedResponse);
                            break;
                        case 409:
                            errorDisplay.style.display = "block";
                            errorDisplay.innerHTML="L'identifiant est déjà utilisé";
                            break;
                    }
                }
            };

            let genders = document.getElementsByName('gender');

            let gender = "";

            for (let i=0; i<genders.length; i++)
                if ( genders[i].checked )
                    gender=genders[i].value;

            const serializedInput = JSON.stringify({
                "login": signupLogin1.value,
                "password": signupPass.value,
                "firstname": signupFirst.value,
                "lastname": signupLast.value,
                "birthdate": signupDate.value,
                "genre": gender,
            });

            xhttp.send(serializedInput);
        } catch (error) {
            errorDisplay.style.display = "block";
            errorDisplay.innerHTML="Erreur Interne - Veuillez réessayer plus tard";
        }
    }else{
        errorDisplay.style.display = "block";
        errorDisplay.innerHTML="Le login est différent";
    }
}

checkTokenValidity(function (htmlCode) {
   switch (htmlCode){
       case 200:
            mainNotSignedIn.remove();
            loginForm.remove();
           break;

       case 403:
           mainSignedIn.remove();
           break;

       case 500:
           mainSignedIn.remove();
           mainNotSignedIn.innerHTML = "Erreur 500, veuillez réessayer plus tard";
           break;
   }
});


function login (login, password) {
    try {
        let xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/login", false);
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                const response = this.responseText;
                const parsedResponse = JSON.parse(response);

                switch (xhttp.status){
                    case 200:
                        localStorage.setItem("token", parsedResponse.token);
                        window.location.replace("/");
                        break;
                    case 403:
                        errorDisplay.style.display = "block";
                        errorDisplay.innerHTML="Les identifiants sont incorrects";
                        break;
                    case 400:
                        handle400(parsedResponse);
                        break;
                    case 500:
                        errorDisplay.style.display = "block";
                        errorDisplay.innerHTML="Erreur Interne - Veuillez réessayer plus tard";
                        break;
                }
            }
        };

        const serializedInput = JSON.stringify({
            "login": login,
            "password": password,
        });

        xhttp.send(serializedInput);
    } catch (error) {
        errorDisplay.style.display = "block";
        errorDisplay.innerHTML="Erreur Interne - Veuillez réessayer plus tard";
    }
}

function handle400(JsonResponse){
    errorDisplay.style.display = "block";
    let problem = "";

    switch (JsonResponse.error.info.case){
        case 0:
            errorDisplay.innerHTML="Veuillez remplir tous les champs";
            break;
        case 1:
            problem = "est trop long";
            break;
        case 2:
            problem = "a un format invalide";
            break;
        case 3:
            problem = "est trop court";
            break;
    }

    if (problem !== ""){
        switch (JsonResponse.error.info.parameter){
            case "login":
                errorDisplay.innerHTML="Le contenu du champ login "+problem;
                break;
            case "password":
                errorDisplay.innerHTML="Le contenu du champ mot de passe "+problem;
                break;
            case "firstname":
                errorDisplay.innerHTML="Le contenu du champ prénom "+problem;
                break;
            case "lastname":
                errorDisplay.innerHTML="Le contenu du champ nom de famille "+problem;
                break;
            case "genre":
                errorDisplay.innerHTML="Le genre donné est invalide";
                break;
            case "birthdate":
                errorDisplay.innerHTML="Le contenu du champ date de naissance "+problem;
                break;
        }
    }

}