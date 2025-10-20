import React, {useState} from 'react';
import Card from '../card/card';
import PropTypes from 'prop-types';
import filmProp from '../card/card.prop';

function FilmList(props) {
  const {films} = props;
  const [activeCard, setActiveCard] = useState(null);

  return (
    <div className='catalog__films-list'>
      {
        films.map((film) => {
          const filmId = film.id;
          return (
            <Card
              key={filmId}
              film={film}
              mouseEnterHandler={() => {
                setActiveCard(filmId);
              }}
              mouseLeaveHandler={() => {
                setActiveCard(null);
              }}
              isActive={film.id === activeCard}
            />
          );
        })
      }
    </div>
  );
}

FilmList.propTypes = {
  films: PropTypes.arrayOf(filmProp).isRequired,
};

export default FilmList;
