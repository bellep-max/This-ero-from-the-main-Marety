<ul>
    @foreach ($result as $key => $child)

            
        
        @if (count($child))
            @foreach ($child as $childKey => $item)
               
               
                @if($childKey  === 'final_errors')
                    @if (is_array($item))
                        @foreach ($item as $finalKey => $finalErrors)
                            @foreach ($finalErrors as $finalError)
                                <li>Root {{ explode("_", $key)[1] + 1 }} Final {{$finalKey + 1}} : {{ $finalError }} </li>
                            @endforeach
                            
                        @endforeach  
                    @endif
                    
                @else
                     <li>Root {{ explode("_", $key)[1]  + 1}} : {{ $item }} </li> 
                @endif 
            @endforeach
            
        @endif
    @endforeach
</ul>