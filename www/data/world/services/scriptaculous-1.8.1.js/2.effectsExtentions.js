Effect.Transitions.exponential = function(pos) 
{
  return 1-Math.pow(1-pos,2);
}