import React from "react";
import { ChevronLeft, ChevronRight } from "react-feather";
import { useState, useEffect } from "react";
import taxiOne from "../assets/car1.jpg";
import taxiTwo from "../assets/car2.jpg";
import taxiThree from "../assets/car3.jpg";
import taxiFour from "../assets/car4.jpg";
import taxiFive from "../assets/car5.jpg";


const images = [taxiOne,taxiTwo,taxiThree,taxiFour,taxiFive]; 

const SliderCarousel = ({  autoSlide = false, autoSlideInterval = 3000 }) => {
  const [currentSlide, setCurrentSlide] = useState(0);

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev === 0 ? images.length - 1 : prev - 1));
  };

  const nextSlide = () => {
    setCurrentSlide((prev) => (prev === images.length - 1 ? 0 : prev + 1));
  };

  

  useEffect(() => {
    if (!autoSlide) return;

    const slideInterval = setInterval(nextSlide, autoSlideInterval);

    return () => clearInterval(slideInterval);
  }, [images, autoSlide, autoSlideInterval, nextSlide]);

  

  return (
    <div className="w-full overflow-hidden relative rounded-md"> {/* Container */}
      <div className="flex transition-transform ease-out duration-500"> {/* Images */}
        {images.map((image, index) => (
          <img key={index} src={image} alt="" className={`w-full h-full object-cover ${currentSlide === index ? "block" : "hidden"}`} />
        ))}
      </div>
      <div className="absolute inset-0 flex items-center justify-between p-4"> {/* Controls */}
        <button className=" bg-white bg-opacity-50 text-black rounded-full p-2 " onClick={prevSlide}>
          <ChevronLeft size={20} />
        </button>
        <button className=" bg-white bg-opacity-50 text-black rounded-full p-2" onClick={nextSlide}>
          <ChevronRight size={20} />
        </button>
      </div>
     
    </div>
  );
};

export default SliderCarousel;
