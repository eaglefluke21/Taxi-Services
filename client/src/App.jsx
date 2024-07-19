import Home from "./pages/Home";
import Book from "./pages/Book";
import Services from "./pages/Services";
import { BrowserRouter as  Router, Routes, Route} from "react-router-dom";

const App = () => {

  return (
<>
<Router>
  <Routes>

<Route path="/" element={<Home/>}/>
<Route path="/Services" element={<Services/>}/>
<Route path="/Book" element={<Book/>}/>

  </Routes>
</Router>
</>    
  )
}


export default App;