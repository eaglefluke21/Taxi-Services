import React from 'react';
import Header from '../components/Header.jsx';
import Footer from '../components/Footer.jsx';
import Carousel from '../components/Carousel.jsx';

const Home = () => {
  return (
    <div className="flex flex-col min-h-screen ">

    <Header />

    <div className=" flex flex-col items-center flex-grow justify-center  rounded-lg bg-white  lg:pb-0">

        <h1 className=" text-xl text-center font-anta text-black pt-4 sm:hidden ">

            Welcome to Go Cab

        </h1>

        <Carousel />


    </div>

    <Footer />



</div>


  );
};

export default Home;