import React ,{ useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
import LoginImage from "../assets/loginimage.jpg";
import { NavLink } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import Popup from "../components/Popup";



function Login() {

    const [PopupVisible, setPopupVisible] = useState(false);

    
    const closePopup = () => {
        setPopupVisible(false);
    };


    const navigate = useNavigate();
    
    const [Formdata, setFormdata] = useState({
        email:'',
        password:'',
    })

   const handleChange = (e) => {

        setFormdata({
            ...Formdata,[e.target.id] : e.target.value
        })

    }

   const handleSubmit = async(e) => {

        e.preventDefault();


        try{
            
            const apiUrl = import.meta.env.VITE_API_URL;
            console.log("current env", apiUrl);

            const response = await axios.post(`${apiUrl}/userauth`, {
                action: 'login',
                ...Formdata
            });

          
            if (response.status === 200) {

                

                const data = response.data;

                console.log(data);

                sessionStorage.setItem('jwToken', data.token);
                console.log('Login successful');

                navigate('/');
            } else {

                setPopupVisible(true);  
                console.error('Login failed:', response.data.message);
            }
        } catch (error) {
            setPopupVisible(true);  

            console.log("Error occurred while logging in:", error);
        }
    }



    return (
        <div className="flex flex-col min-h-screen ">
        <Header/>

        <div className="flex flex-col flex-grow lg:flex-row lg:justify-evenly bg-gradient-to-r from-green-100 to-stone-200 py-16 rounded-md">

            <form className="flex flex-col justify-center px-6 sm:px-40 lg:px-20 lg:w-[40rem]" onSubmit={handleSubmit}>

            <p className="font-quick text-3xl font-bold mb-10">Log In to Your Account</p>

            <div className="mb-10">
            <label className="font-quick text-lg font-bold "> Email Address </label>
            <input id="email" type="email" value={Formdata.email} className="w-full border-gray-700  border rounded-md py-1 font-quick ps-4 font-semibold " onChange={handleChange}></input>
            </div>

            <div className="mb-10">
            <label className="font-quick text-lg font-bold "> Password </label>
            <input id="password" type="password" value={Formdata.password} className="w-full border-gray-700  border rounded-md py-1 font-quick ps-4 font-semibold" onChange={handleChange}></input>
            </div>


            <button className="w-full bg-black rounded-md py-1.5 font-quick font-semibold shadow-sm shadow-black text-white "> Log In</button>

            <p className="font-quick font-semibold  mx-auto mt-2 "> Don't Have an Account. <NavLink to="/Signup"> <span className="font-quick font-bold hover:underline cursor-pointer">Create a New Account.</span></NavLink></p>
            


            </form>  

            <img src={LoginImage} className="rounded-md shadow-md object-cover hidden lg:block"/>

        </div>

        <div>
            {
                PopupVisible === true && 
                    <Popup isOpen={true} message="Incorrect Login Credentials" onClose={closePopup} />
                
            }

        </div>


        <Footer/>

        </div>
    );
};

export default Login;